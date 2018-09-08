<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: plugin.proto

namespace Google\Protobuf\Compiler;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The version number of protocol compiler.
 *
 * Generated from protobuf message <code>google.protobuf.compiler.Version</code>
 */
class Version extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int32 major = 1;</code>
     */
    private $major = 0;
    /**
     * Generated from protobuf field <code>int32 minor = 2;</code>
     */
    private $minor = 0;
    /**
     * Generated from protobuf field <code>int32 patch = 3;</code>
     */
    private $patch = 0;
    /**
     * A suffix for alpha, beta or rc release, e.g., "alpha-1", "rc2". It should
     * be empty for mainline stable releases.
     *
     * Generated from protobuf field <code>string suffix = 4;</code>
     */
    private $suffix = '';

    public function __construct()
    {
        \GPBMetadata\Google\Protobuf\Plugin::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>int32 major = 1;</code>
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * Generated from protobuf field <code>int32 major = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setMajor($var)
    {
        GPBUtil::checkInt32($var);
        $this->major = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 minor = 2;</code>
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * Generated from protobuf field <code>int32 minor = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setMinor($var)
    {
        GPBUtil::checkInt32($var);
        $this->minor = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 patch = 3;</code>
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * Generated from protobuf field <code>int32 patch = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setPatch($var)
    {
        GPBUtil::checkInt32($var);
        $this->patch = $var;

        return $this;
    }

    /**
     * A suffix for alpha, beta or rc release, e.g., "alpha-1", "rc2". It should
     * be empty for mainline stable releases.
     *
     * Generated from protobuf field <code>string suffix = 4;</code>
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * A suffix for alpha, beta or rc release, e.g., "alpha-1", "rc2". It should
     * be empty for mainline stable releases.
     *
     * Generated from protobuf field <code>string suffix = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setSuffix($var)
    {
        GPBUtil::checkString($var, true);
        $this->suffix = $var;

        return $this;
    }
}
