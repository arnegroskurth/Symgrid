<?php

namespace ArneGroskurth\Symgrid\Grid\Export;

use ArneGroskurth\Symgrid\Grid\AbstractExport;
use ArneGroskurth\Symgrid\Grid\Constants;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class HTMLExport extends AbstractExport {
    
    const HTML_HEAD = <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>%s</title>
    </head>
    <body>
HTML;

    const HTML_FOOT = <<<HTML
    </body>
</html>
HTML;



    /**
     * {@inheritdoc}
     */
    public function render($locale = null) {

        $this->disableSymfonyProfiler();

        $columnListIterator = $this->grid->getColumnList()->getIterator();

        $dataSource = $this->grid->getDataSource();
        $dataSource->load($this->grid->getColumnList());


        $content = sprintf(self::HTML_HEAD, $this->grid->getExportFileName());
        $content .= '<table><thead><tr>';

        foreach($columnListIterator as $column) {

            $content .= sprintf('<th>%s</th>', $this->translate($column->getTitle(), $locale));
        }

        $content .= '</tr></thead><tbody>';

        foreach($dataSource as $dataRecord) {

            $content .= '<tr>';

            foreach($columnListIterator as $column) {

                $content .= sprintf('<td>%s</td>', $column->render($dataRecord, Constants::TARGET_HTML));
            }

            $content .= '</tr>';
        }

        $content .= '</tbody></table>' . self::HTML_FOOT;


        $response = new Response($content);
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.html', $this->grid->getExportFileName())));
        $response->headers->set('Content-Length', strlen($content));
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}