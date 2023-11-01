<?php

namespace App\Controller;

use App\Entity\Folders;
use App\Util\AwsS3Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class S3FolderController extends AbstractController
{
    /**
     * @Route("/s3/folder", name="s3_folder")
     */
    public function s3Folder(Request $request, AwsS3Util $awsS3Util): Response
    {
        $awsS3Util->setClient($_ENV["AWS_S3_NAS_ACCESS_KEY"],$_ENV["AWS_S3_NAS_SECRET_KEY"]);
        $s3Client = $awsS3Util->getClient();
        $bucketName = $_ENV["NAS_SYNC_BUCKET"];
        $objects = $s3Client->ListObjects(['Bucket' => $bucketName , 'Delimiter'=>'/']);

        $prefixes = $objects->get('CommonPrefixes');
        $prefixList = [];
        foreach ($prefixes as $prefix){
            $prefixList[] = $prefix;
        }

        return $this->json($prefixList);
    }

    /**
     * @Route("/s3/sub/folder", name="s3_sub_folder")
     */
    public function s3SubFolder(Request $request, AwsS3Util $awsS3Util, EntityManagerInterface $entityManager): Response
    {
        // 'SanF2-iVnews/'
        $prefix = $request->get('Prefix', null);
        $awsS3Util->setClient($_ENV["AWS_S3_NAS_ACCESS_KEY"],$_ENV["AWS_S3_NAS_SECRET_KEY"]);
        $s3Client = $awsS3Util->getClient();
        $bucketName = $_ENV["NAS_SYNC_BUCKET"];
        $params = ['Bucket' => $bucketName , 'Delimiter'=>'/' ];
        if($prefix){
            $params['Prefix'] = $prefix;
        }
        $objects = $s3Client->ListObjects($params);


        $prefixes = $objects->get("CommonPrefixes");
        $prefixList = [];

        foreach ($prefixes as $prefix){
            $foldersExist = $entityManager->getRepository(Folders::class)->findOneBy(['subFolder' => $prefix]);
            if(empty($foldersExist)){
                $prefixList[] = $prefix;
            }
        }
        return $this->json($prefixList);
    }
}
