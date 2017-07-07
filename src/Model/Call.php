<?php
namespace Maschinenraum\FbApi\Model;

class Call
{

    const TYPE_INCOMING = 1;
    const TYPE_INCOMING_MISSED = 2;
    const TYPE_OUTGOING = 3;

    /**
     * @var string
     */
    protected $called;

    /**
     * @var string
     */
    protected $calledNumber;

    /**
     * @var string
     */
    protected $caller;

    /**
     * @var string
     */
    protected $callerNumber;

    /**
     * @var \DateTimeInterface
     */
    protected $date;

    /**
     * @var string
     */
    protected $device;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $type;

    /**
     * Call constructor.
     * @param \SimpleXMLElement $callXml
     */
    public function __construct(\SimpleXMLElement $callXml)
    {
        $this->called = (string) $callXml->Called;
        $this->calledNumber = (string) $callXml->CalledNumber;
        $this->caller = (string) $callXml->Caller;
        $this->callerNumber = (string) $callXml->CallerNumber;
        $this->date = \DateTime::createFromFormat('d.m.y H:i', (string) $callXml->Date, new \DateTimeZone('Europe/Berlin'));
        $this->device = trim((string) $callXml->Device);
        $this->id = (string) $callXml->Id;
        $this->name = (string) $callXml->Name;
        $this->type = (int) $callXml->Type;
    }

    /**
     * @return string
     */
    public function getCalled()
    {
        return $this->called;
    }

    /**
     * @return string
     */
    public function getCalledNumber()
    {
        return $this->calledNumber;
    }

    /**
     * @return string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * @return string
     */
    public function getCallerNumber()
    {
        return $this->callerNumber;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        switch ($this->type) {
            case self::TYPE_INCOMING:
                return 'Incoming';
            case self::TYPE_INCOMING_MISSED:
                return 'Missed';
            case self::TYPE_OUTGOING;
                return 'Outgoing';
            default:
                return 'Unknown';
        }
    }

    /**
     * @return string
     */
    public function getLocalNumber()
    {
        if ($this->type === self::TYPE_OUTGOING) {
            return $this->getCallerNumber();
        } else {
            return $this->getCalledNumber();
        }
    }

    public function getExternalNumber()
    {
        if ($this->type === self::TYPE_OUTGOING) {
            return $this->getCalled();
        } else {
            return $this->getCaller();
        }
    }
}
