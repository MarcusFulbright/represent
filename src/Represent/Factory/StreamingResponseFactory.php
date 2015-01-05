<?php

namespace Represent\Factory;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamingResponseFactory
{
    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @param PaginationFactory $paginationFactory
     */
    public function __construct(PaginationFactory $paginationFactory)
    {
        $this->paginationFactory = $paginationFactory;
    }

    /**
     * Handles creating a streaming response for the given file path and uses the given file name
     *
     * @param        $filePath
     * @param        $fileName
     * @param string $type
     * @return StreamedResponse
     */
    public function createStreamingResponse($filePath, $fileName, $type = 'application/octet-stream')
    {
        $response = new StreamedResponse();
        $this->setStreamCallBack($response, $filePath);
        $this->setStreamHeaders($response, $fileName, $type);

        return $response;
    }


    /**
     * @param $response
     * @param $fileName
     * @param $type
     */
    private function setStreamHeaders($response, $fileName, $type)
    {
        $this->setContentDisposition($response, $fileName);
        $this->setStreamContentType($response, $type);
    }

    /**
     * @param StreamedResponse $response
     * @param                  $fileName
     * @return StreamedResponse
     */
    private function setContentDisposition(StreamedResponse $response, $fileName)
    {
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $fileName
            )
        );

        return $response;
    }

    /**
     * @param StreamedResponse $response
     * @param                  $type
     * @return StreamedResponse
     */
    private function setStreamContentType(StreamedResponse $response, $type)
    {
        $response->headers->set('Content-Type', $type);

        return $response;
    }

    /**
     * @param StreamedResponse $response
     * @param                  $filePath
     * @return StreamedResponse
     */
    private function setStreamCallBack(StreamedResponse $response, $filePath)
    {
        $response->setCallback(
            function() use ($filePath) {
                $stream = fopen($filePath, 'r');

                if (!$stream) {
                    throw new \Exception('File Not Found');
                }

                while (!feof($stream)) {
                    echo fread($stream, 1024);
                }

                fclose($stream);
                ob_flush();
                flush();
            }
        );

        return $response;
    }
}