<?php
namespace wbx\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class FileController extends Controller {

    public function downloadAction($id, $class) {
        $em = $this->getDoctrine()->getEntityManager();
        $file = $em->getRepository($class)->find($id);

        if (!$file) {
            throw $this->createNotFoundException("Unable to find File");
        }

        $path = '../web/' . $file->getDownloadPath();
        $options = array(
            'serve_filename' => $file->getDownloadFilename()
        );

        $response = $this->get('igorw_file_serve.response_factory')->create($path, 'application/octet-stream', $options);

        return $response;
    }

}


