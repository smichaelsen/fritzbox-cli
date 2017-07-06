<?php

namespace Maschinenraum\FbApi\Service;

use Maschinenraum\FbApi\Model\Call;

class CallListService
{

    const FILTER_DEVICE = 'filter_device';
    const FILTER_EXTERNAL_NUMBER = 'filter_external_number';
    const FILTER_LOCAL_NUMBER = 'filter_local_number';

    /**
     * @param array $filters
     * @return array|Call[]
     */
    public static function getCalls($filters = null)
    {
        $apiService = new FritzBoxApiService();
        $callsXmlString = $apiService->request('calllist.lua');
        $cl = simplexml_load_string($callsXmlString);
        $calls = [];
        foreach ($cl->Call as $callXml) {
            $call = new Call($callXml);
            if (isset($filters[self::FILTER_DEVICE]) && !self::filterDevice($call, $filters[self::FILTER_DEVICE])) {
                continue;
            }
            if (isset($filters[self::FILTER_EXTERNAL_NUMBER]) && !self::filterExternalNumber($call, $filters[self::FILTER_EXTERNAL_NUMBER])) {
                continue;
            }
            if (isset($filters[self::FILTER_LOCAL_NUMBER]) && !self::filterLocalNumber($call, $filters[self::FILTER_LOCAL_NUMBER])) {
                continue;
            }
            $calls[] = $call;
        }
        return $calls;
    }

    /**
     * @param Call $call
     * @param string $deviceName
     * @return bool
     */
    protected static function filterDevice(Call $call, $deviceName)
    {
        return $call->getDevice() === $deviceName;
    }

    /**
     * @param Call $call
     * @param $externalNumber
     * @return bool
     */
    protected static function filterExternalNumber(Call $call, $externalNumber)
    {
        return $call->getExternalNumber() === $externalNumber;
    }

    /**
     * @param Call $call
     * @param $localNumber
     * @return bool
     */
    protected static function filterLocalNumber(Call $call, $localNumber)
    {
        return $call->getLocalNumber() === $localNumber;
    }

}
