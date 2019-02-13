<?php

namespace App\Controller\api;

use App\Entity\Invoice;
use App\Services\InvoiceService;
use App\Services\ValidateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidateService
     */
    private $validateService;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    public function __construct(SerializerInterface $serializer, ValidateService $validateService, InvoiceService $invoiceService)
    {
        $this->serializer = $serializer;
        $this->validateService = $validateService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * @Route("/api/invoices", methods={"POST"})
     */
    public function createInvoiceAction(Request $request)
    {
        $invoice = $this->serializer->deserialize($request->getContent(), Invoice::class, 'json');
        $this->validateService->validate($invoice);

        $this->getUser()->addInvoice($invoice);

        $this->getDoctrine()->getManager()->persist($invoice);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['invoice' => $invoice]);
    }

    /**
     * @Route("/api/invoices/{invoice}/pay", methods={"POST"})
     */
    public function payInvoiceAction(Invoice $invoice)
    {
        $this->invoiceService->payInvoice($invoice);

        return $this->json(['invoice' => $invoice]);
    }
}
