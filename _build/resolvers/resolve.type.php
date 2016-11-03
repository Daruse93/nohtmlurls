<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

    // Получаем или создаём новый тип содержимого
    $name = 'HTML-NO';
    if (!$contentType = $modx->getObject('modContentType', array('name' => $name))) {
        $contentType = $modx->newObject('modContentType');
        $contentType->set('name', $name);
        $contentType->set('mime_type', 'text/html');
        $contentType->set('file_extensions', '');
        $contentType->save();
        $contentTypeID = $contentType->get('id');
    }else{
        $contentTypeID = $contentType->get('id');
    }

    // Записываем в системный настройки, как тип содержимого по умолчанию
    if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'default_content_type'))) {
        $tmp = $modx->newObject('modSystemSetting');
    }
    $tmp->fromArray(array(
        'namespace' => 'core',
        'area'      => 'site',
        'xtype'     => 'modx-combo-content-type',
        'value'     => $contentTypeID,
        'key'       => 'default_content_type',
    ), '', true, true);
    $tmp->save();

    // Получаем все типы содержимого с mime_types = 'text/html'
    $contentTypes = $modx->getCollection('modContentType');

    foreach($contentTypes as $contentType){
        $mime_type = $contentType->get('mime_type');
        if($mime_type == 'text/html'){
            $contentTypeids[] = $contentType->get('id');
        }
    }

    // Пробигаемся по всем с ресурсам с типом содержимого с mime_types = 'text/html' и записываем им новый тип содержимого
    $resources = $modx->getCollection('modResource');
 
    foreach ($resources as $resource) {
        $contenttype = $resource->get('content_type');
        if (in_array($contenttype, $contentTypeids)) {
            $resource->set('content_type',$contentTypeID);
            $resource->save();
        }
    }

        
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;