<?php
namespace Maschinenraum\FbApi\Service;

use Maschinenraum\FbApi\Model\Contact;

class PhoneBookService
{

    /**
     * @return array|Contact[]
     */
    public static function getContacts()
    {
        $apiService = new FritzBoxApiService();
        $phonebookXmlString = $apiService->request('phonebook.lua');
        echo $phonebookXmlString;
    }

}