<?php
$cache	=	$this->getHelper('nCache');
$cache->cacheBegin($this->toSingleDs($this->getCacheFolder().DS.'template'.DS.'error_footer.html'));
if(!$cache->isCached()) {
?>

<footer class="nbr_error_footer">Copyright &reg;<?php echo date('Y') ?> nUbersoft.</footer>
<?php
}
echo $cache->cacheRender();