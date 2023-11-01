<?php
/**
 * Created by PhpStorm.
 * User: ngpatel
 * Date: 6/5/21
 * Time: 9:28 AM
 */

namespace App\Services\Vne;


use Doctrine\Persistence\ObjectRepository;

interface VneServiceInterface
{

    /**
     * @param array $response
     * @param $currentUrl
     * @param $page
     * @param $itemPerPage
     * @param $filter
     * @return array
     */
    public function handleAllResponse(array $response, $currentUrl ,$page, $itemPerPage , $filter): array;

    /**
     * @param $response
     * @param $currentUrl
     * @param $filter
     * @return array
     */
    public function handleResponse($response, $currentUrl, $filter): array;

    /**
     * @param $currentUrl
     * @param $page
     * @param $itemPerPage
     * @param $filter
     * @return array
     */
    public function index($currentUrl , $page, $itemPerPage, $filter): array;

    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @return ObjectRepository
     */
    public function getRepository():ObjectRepository;

    /**
     * @param $id
     * @return mixed
     */
    public function post($id);

    /**
     * @param $id
     * @return mixed
     */
    public function put($id);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);
}