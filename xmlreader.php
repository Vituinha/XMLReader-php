<?php
/*
 *@param $xml is the archive .xml that you want to read
 * after loading the archive, the code will read-it and turn
 * into an array that you can use as you please
 * https://github.com/Vituinha 04/28/2022
 */

function XMLtoArray($xml)
{
    $previous_value = libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->loadXml($xml);
    libxml_use_internal_errors($previous_value);
    if (libxml_get_errors()) {
        return [];
    }
    return DOMtoArray($dom);
}

function DOMtoArray($root)
{
    $result = array();

    if ($root->hasAttributes()) {
        $attrs = $root->attributes;
        foreach ($attrs as $attr) {
            $result['@attributes'][$attr->name] = $attr->value;
        }
    }

    if ($root->hasChildNodes()) {
        $children = $root->childNodes;
        if ($children->length == 1) {
            $child = $children->item(0);
            if (in_array($child->nodeType, [XML_TEXT_NODE,XML_CDATA_SECTION_NODE])) {
                $result['_value'] = $child->nodeValue;
                return count($result) == 1
                    ? $result['_value']
                    : $result;
            }
        }
        $groups = array();
        foreach ($children as $child) {
            if (!isset($result[$child->nodeName])) {
                $result[$child->nodeName] = DOMtoArray($child);
            } else {
                if (!isset($groups[$child->nodeName])) {
                    $result[$child->nodeName] = array($result[$child->nodeName]);
                    $groups[$child->nodeName] = 1;
                }
                $result[$child->nodeName][] = DOMtoArray($child);
            }
        }
    }
    return $result;
}

$xml = file_get_contents('archive.xml');
$xml = XMLtoArray($xml);
$xml = (array)$xml['test'];
$tt = 0;
foreach ($xml['read']['here'] as $k => $v) {
    $tt = $tt + ($v['line']);
}
echo $tt;
?>