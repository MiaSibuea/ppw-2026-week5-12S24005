<?php

namespace App\Http\Controllers\Soap;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleXMLElement;

class LibrarySoapController extends Controller
{
    private const SOAP_NAMESPACE = 'http://schemas.xmlsoap.org/soap/envelope/';

    public function wsdl(): Response
    {
        $serviceUrl = htmlspecialchars(url('/soap/sipus'), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $wsdl = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <definitions xmlns="http://schemas.xmlsoap.org/wsdl/"
                         xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
                         xmlns:tns="urn:sipus-del:clearance"
                         xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                         targetNamespace="urn:sipus-del:clearance"
                         name="LibraryClearanceService">
              <types>
                <xsd:schema targetNamespace="urn:sipus-del:clearance" elementFormDefault="qualified">
                  <xsd:element name="CheckClearanceStatus">
                    <xsd:complexType><xsd:sequence><xsd:element name="nim_peminjam" type="xsd:string"/></xsd:sequence></xsd:complexType>
                  </xsd:element>
                  <xsd:element name="CheckClearanceStatusResponse">
                    <xsd:complexType><xsd:sequence><xsd:element name="active_loans" type="xsd:integer"/><xsd:element name="is_cleared" type="xsd:boolean"/></xsd:sequence></xsd:complexType>
                  </xsd:element>
                </xsd:schema>
              </types>
              <message name="CheckClearanceStatusInput"><part name="parameters" element="tns:CheckClearanceStatus"/></message>
              <message name="CheckClearanceStatusOutput"><part name="parameters" element="tns:CheckClearanceStatusResponse"/></message>
              <message name="CheckClearanceStatusFault"><part name="fault" type="xsd:anyType"/></message>
              <portType name="LibraryClearancePortType">
                <operation name="CheckClearanceStatus">
                  <input message="tns:CheckClearanceStatusInput"/>
                  <output message="tns:CheckClearanceStatusOutput"/>
                  <fault name="SoapFault" message="tns:CheckClearanceStatusFault"/>
                </operation>
              </portType>
              <binding name="LibraryClearanceBinding" type="tns:LibraryClearancePortType">
                <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
                <operation name="CheckClearanceStatus">
                  <soap:operation soapAction="urn:sipus-del:clearance/CheckClearanceStatus"/>
                  <input><soap:body use="literal"/></input>
                  <output><soap:body use="literal"/></output>
                  <fault name="SoapFault"><soap:fault name="SoapFault" use="literal"/></fault>
                </operation>
              </binding>
              <service name="LibraryClearanceService">
                <port name="LibraryClearancePort" binding="tns:LibraryClearanceBinding"><soap:address location="{$serviceUrl}"/></port>
              </service>
            </definitions>
            XML;

        return response($wsdl)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function checkClearance(Request $request): Response
    {
        $xml = $request->getContent();

        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $xml)) {
            return $this->fault('Permintaan XML tidak valid.');
        }

        $previous = libxml_use_internal_errors(true);
        $envelope = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($envelope === false || $envelope->getName() !== 'Envelope') {
            return $this->fault('Envelope SOAP tidak valid.');
        }

        $namespaces = $envelope->getNamespaces(true);
        if (($namespaces[''] ?? null) !== self::SOAP_NAMESPACE && ($namespaces['soap'] ?? null) !== self::SOAP_NAMESPACE) {
            return $this->fault('Namespace SOAP tidak didukung.');
        }

        $nodes = $envelope->xpath('//*[local-name()="nim_peminjam"]');
        $nim = isset($nodes[0]) ? trim((string) $nodes[0]) : '';
        if ($nim === '' || mb_strlen($nim) > 30) {
            return $this->fault('Elemen nim_peminjam wajib diisi dan maksimal 30 karakter.');
        }

        $activeLoans = Peminjaman::query()
            ->where('nim_peminjam', $nim)
            ->where('status', 'dipinjam')
            ->count();
        $isCleared = $activeLoans === 0 ? 'true' : 'false';

        $soapNamespace = self::SOAP_NAMESPACE;
        $response = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="{$soapNamespace}" xmlns:tns="urn:sipus-del:clearance">
              <soap:Header/>
              <soap:Body>
                <tns:CheckClearanceStatusResponse>
                  <tns:active_loans>{$activeLoans}</tns:active_loans>
                  <tns:is_cleared>{$isCleared}</tns:is_cleared>
                </tns:CheckClearanceStatusResponse>
              </soap:Body>
            </soap:Envelope>
            XML;

        return response($response)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    private function fault(string $message): Response
    {
        $safeMessage = htmlspecialchars($message, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $soapNamespace = self::SOAP_NAMESPACE;
        $fault = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="{$soapNamespace}">
              <soap:Header/>
              <soap:Body>
                <soap:Fault>
                  <faultcode>soap:Client</faultcode>
                  <faultstring>{$safeMessage}</faultstring>
                </soap:Fault>
              </soap:Body>
            </soap:Envelope>
            XML;

        return response($fault, 500)->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
