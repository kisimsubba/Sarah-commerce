<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
final class ServerSideEncryptionByDefault
{
    private $sseAlgorithm;
    private $kmsMasterKeyId;
    public function __construct(array $input)
    {
        $this->sseAlgorithm = $input['SSEAlgorithm'] ?? null;
        $this->kmsMasterKeyId = $input['KMSMasterKeyID'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return string|null
     */
    public function getKmsMasterKeyId()
    {
        return $this->kmsMasterKeyId;
    }
    public function getSseAlgorithm() : string
    {
        return $this->sseAlgorithm;
    }
}
