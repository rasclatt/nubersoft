<?php
if(empty(trim($values['file_path'])) || empty(trim($values['file_name'])))
    return false;

$nDownloader    =    $nRender->getHelper('nDownloader');
$nImage            =    $nRender->getHelper('ImageFactory');
$salt            =    (!empty($nRender->getFileSalt()))? $nRender->getFileSalt() : false;
$ID                =    $values['ID'];
$path            =    NBR_ROOT_DIR.$values['file_path'].$values['file_name'];
$thumbDir        =    pathinfo($path,PATHINFO_DIRNAME).DS.'thumbs';
$thumb            =    $thumbDir.DS.$values['file_name'];
if($nRender->isDir($thumbDir))
    $nImage->makeThumbnail($path,100,100,$thumb,50);

if(!is_file($thumb))
    $thumb    =    $path;

$URI    =    parse_url($nRender->getDataNode('_SERVER')->REQUEST_URI);
if(!empty($URI['query'])) {
    $query    =    array();
    parse_str($URI['query'],$query);
    $URI['query']    =    $query;
}
else
    $URI['query']    =    array();

$URI['query']    =    array_merge(array(
                        'action'=>'nbr_download_file',
                        'file'=> $nDownloader->encode(array("ID"=>$ID,"table"=>$table),$salt)
                    ),$URI['query']);

$imgButton    =    array(
                    "action"=>"nbr_open_modal",
                    "data"=>array(
                        "deliver"=>array(
                            "ID"=>$ID,
                            "table"=>$table,
                            "action"=>"nbr_component_edit_image",
                            "close_button"=>"CANCEL",
                            "jumppage"=>$nRender->getDataNode('_SERVER')->REQUEST_URI
                        )
                    )
                );
?>
<div class="nbr_standard block">
    <ul class="nbr_standard table cell bottom">
        <li>
            <?php echo $nImage->imageBase64($thumb,array('class'=>'nbr_thumb standard')) ?>
        </li>
        <li>
            <ul class="nbr_standard table row">
                <li>
                    <div>
                        <p style="font-size: 12px;"><?php echo $values['file_name'] ?></p>
                    </div>
                </li>
                <li>
                    <div>
                        <a href="#" class="div_button nTrigger" data-instructions='<?php echo json_encode($imgButton) ?>'>EDIT IMAGE</a>                    
                    </div>
                </li>
                <li>
                    <div>
                        <a class="div_button" href="<?php echo $nRender->siteUrl().$URI['path'].'?'.http_build_query($URI['query']) ?>">DOWNLOAD</a>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>