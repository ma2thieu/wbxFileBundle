<?php
namespace wbx\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class FileController extends Controller {

    public function downloadAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $file = $em->getRepository('wbxFileBundle:File')->find($id);

        if (!$file) {
            throw $this->createNotFoundException("Unable to find File");
        }

        $response = new Response();
        $response->headers->set('Cache-Control', 'public');
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $file->getDownloadFilename()));
        $response->headers->set('Content-Length', filesize($file->getAbsolutePath()));
        $response->setContent(file_get_contents($file->getAbsolutePath()));

        return $response;
    }

}


