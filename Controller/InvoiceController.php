<?php

namespace Hatimeria\BankBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Hatimeria\ExtJSBundle\Annotation\Remote;

use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    /**
     * @Route("/invoice/{id}", name="invoice_download")
     * @Secure("ROLE_USER")
     */
    public function downloadAction($id)
    {
        $isAdmin = $this->get("security.context")->isGranted("ROLE_ADMIN");
        $invoice = $this->get("bank.invoice.manager")->findOneById($id);
        
        if(!$invoice) {
            throw new NotFoundHttpException();
        }
        
        if(!$isAdmin && $invoice->getAccount() != $this->getUser()->getAccount()) {
            throw new AccessDeniedException;
        }
        
        $html = $this->renderView('::invoice.html.twig', array(
            'invoice'  => $invoice,
            'user'     => $invoice->getAccount()->getUser()
        ));
        
        $filename = $invoice->getFullNumber();
        
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => sprintf('attachment; filename="%s.pdf"', $filename)
            )
        );        
    }
    
    /**
     * @Secure("ROLE_USER")
     * 
     * @remote
     */
    public function listAction($params)
    {
        $query = $this->get("bank.invoice.manager")->getQueryForUser($this->getUser());
        
        return $this->get('hatimeria_extjs.pager')->fromQuery($query, $params);
    }
    
    /**
     * @remote
     * 
     * @Secure("ROLE_ADMIN")
     *
     * @param type $params 
     */
    public function allAction($params)
    {
        $class = $this->get("bank.invoice.manager")->getClass();
        
        return $this->get('hatimeria_extjs.pager')->fromEntity($class, $params);
        
    }
}
