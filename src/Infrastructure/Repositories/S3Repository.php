<?php

namespace Osds\Api\Infrastructure\Repositories;

use App\Util\Aws\AwsS3Util;

class S3Repository
{
    /**
     * @param AwsS3Util $aws3_service
     * @param string $filename
     * @param string $filepath
     * @param array $parameters
     * @return string
     * @throws \Exception
     */
    public static function persist(AwsS3Util $aws3_service, string $filename, string $filepath, array $parameters): string
    {
        if (!is_readable($filepath)) {
            throw new \Exception(sprintf('File %s is not readable', $filepath));
        }

        $acl = $parameters['acl'] ?? AwsS3Util::ACL_PRIVATE;

        $s3_url = $aws3_service->put(
            getenv('AWS_IMAGE_BUCKET'),
            $filename,
            $filepath,
            $parameters['folder'],
            $acl
        );

        return $s3_url;
    }

    /**
     * @param AwsS3Util $aws3_service
     * @param string $filename
     * @param string $content
     * @param array $parameters
     * @return string
     */
    public static function persistContent(AwsS3Util $aws3_service, string $filename, string $content, array $parameters): string
    {
        $acl = $parameters['acl'] ?? AwsS3Util::ACL_PRIVATE;

        $s3_url = $aws3_service->putContent(
            getenv('AWS_IMAGE_BUCKET'),
            $filename,
            $content,
            $parameters['folder'],
            $acl
        );

        return $s3_url;
    }

    /**
     * @param AwsS3Util $S3Service
     * @param string $filepath
     * @return bool
     */
    public static function delete(AwsS3Util $s3Service, string $filepath): bool
    {
        return $s3Service->delete(getenv('AWS_IMAGE_BUCKET'), $filepath);
    }
}