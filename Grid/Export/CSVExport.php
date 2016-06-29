<?php

namespace ArneGroskurth\Symgrid\Grid\Export;

use ArneGroskurth\Symgrid\Grid\AbstractExport;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class CSVExport extends AbstractExport {

    /**
     * @var string
     */
    protected $columnSeparator = ';';

    /**
     * @var string
     */
    protected $stringEnclosure = '"';

    /**
     * @var string
     */
    protected $targetEncoding = 'Windows-1252';

    /**
     * @var string
     */
    protected $sourceEncoding = 'UTF-8';


    /**
     * {@inheritdoc}
     */
    public function render($locale = null) {

        if(($fp = tmpfile()) === false) {

            throw new Exception("Could not create temporary export file.");
        }


        $columnListIterator = $this->grid->getColumnList()->getIterator();

        $dataSource = $this->grid->getDataSource();
        $dataSource->load($this->grid->getColumnList());


        // write column titles
        $values = array();
        foreach($columnListIterator as $column) {

            $values[] = mb_convert_encoding($this->translate($column->getTitle(), $locale), $this->targetEncoding, $this->sourceEncoding);
        }
        fputcsv($fp, $values, $this->columnSeparator, $this->stringEnclosure);


        // write content
        foreach($dataSource as $dataRecord) {

            $values = array();
            foreach($columnListIterator as $column) {

                $value = $column->render($dataRecord, Constants::TARGET_CSV);

                $values[] = mb_convert_encoding($value, $this->targetEncoding, $this->sourceEncoding);
            }

            fputcsv($fp, $values, $this->columnSeparator, $this->stringEnclosure);
        }


        // get file size
        $fileSize = ftell($fp);
        rewind($fp);


        $response = new Response(fread($fp, $fileSize));
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.csv', $this->grid->getExportFileName())));
        $response->headers->set('Content-Length', $fileSize);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}