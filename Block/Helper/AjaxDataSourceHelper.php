<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Block\Helper;

use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidArgumentException;
use Sonatra\Bundle\BootstrapBundle\Block\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Helper for generate the AJAX response for the block.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxDataSourceHelper
{
    /**
     * Generates the ajax response.
     *
     * @param Request                                  $request
     * @param DataSourceInterface|BlockInterface|array $source
     * @param string                                   $format
     *
     * @return Response
     *
     * @throws InvalidArgumentException When the format is not allowed
     */
    public static function generateResponse(Request $request, $source, $format = 'json')
    {
        $formats = array('xml', 'json');

        if (!in_array($format, $formats)) {
            $msg = "The '%s' format is not allowed. Try with '%s'";
            throw new InvalidArgumentException(sprintf($msg, $format, implode("', '", $formats)));
        }

        if ($source instanceof BlockInterface) {
            $source = $source->getData();
        }

        if (!$source instanceof DataSourceInterface) {
            throw new InvalidArgumentException('The "source" argument of AjaxDataSourceHelper must be an instance of DataSourceInterface or instance of BlockInterface contains the DataSource instance in data option');
        }

        /* @var DataSourceInterface $source */
        $data = static::getData($request, $source);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/' . $format);
        $response->setContent($serializer->serialize($data, $format));

        return $response;
    }

    /**
     * Gets the ajax data.
     *
     * @param Request             $request
     * @param DataSourceInterface $source
     * @param string              $prefix
     *
     * @return array
     */
    public static function getData(Request $request, DataSourceInterface $source, $prefix = '')
    {
        $source->setPageSize(intval($request->get($prefix.'ps')));
        $source->setPageNumber(intval($request->get($prefix.'pn')));
        $source->setSortColumns($request->get($prefix.'sc', array()));
        $source->setParameters($request->get($prefix.'p', array()));

        return array(
            'rows' => $source->getRows(),
            'size' => $source->getSize(),
            'pageSize' => $source->getPageSize(),
            'pageNumber' => $source->getPageNumber(),
            'pageCount' => $source->getPageCount(),
            'sortColumns' => $source->getSortColumns(),
        );
    }
}
