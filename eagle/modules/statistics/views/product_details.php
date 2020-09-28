<?php
use eagle\modules\util\helpers\TranslateHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Dialog;
use yii\data\Sort;
use eagle\modules\purchase\helpers\PurchaseHelper;
use eagle\assets\JuiAsset;

$tmp_js_version = '1.01';
$baseUrl = \Yii::$app->urlManager->baseUrl;

$this->registerJsFile ( $baseUrl . "/js/jquery.json-2.4.js", [
		'depends' => [
		JuiAsset::class,
		'yii\bootstrap\BootstrapPluginAsset'
	]
] );
$this->registerCssFile ( $baseUrl . "/css/statistics/statistics.css" );

$this->registerJsFile($baseUrl."/js/project/statistics/statisticsList.js?v=".$tmp_js_version, ['depends' => ['yii\web\JqueryAsset']]);
$this->registerJsFile($baseUrl."/js/project/statistics/product_details.js?v=".$tmp_js_version, ['depends' => ['yii\web\JqueryAsset']]);
$this->registerJsFile($baseUrl."js/project/catalog/downloadexcel.js", ['depends' => ['yii\web\JqueryAsset']]);
$this->registerJsFile($baseUrl."js/project/util/select_country.js?v=".$tmp_js_version, ['depends' => [JuiAsset::class,'yii\bootstrap\BootstrapPluginAsset']]);

$this->registerJs("statistics.list.init();" , \yii\web\View::POS_READY);
$this->registerJs("product_details.init();" , \yii\web\View::POS_READY);
$this->registerJs("$.initQtip();" , \yii\web\View::POS_READY);

?>

<style>
.div_inner_td{
	width: 100%;
}
.span_inner_td{
	float: left;
	padding: 6px 0px;
}
.div_choose
{
	border: 1px solid #ccc;
	width:90%;
	padding:5px 0 5px 20px;
	float:left;
}
.div_choose > span
{
	font-size: 15px; 
	line-height: 24px;
	float:left;
}
th
{
	text-align: center !important;
}
.lb_sort
{
	color: #FFFFFF;
	background-color: #ff9900;
	padding: 0px 5px;
}
</style>

<div class="flex-row">
	<!-- 左侧标签快捷区域 -->
	<?= $this->render('/_menu') ?>
	<?php if(isset($ischeck) && $ischeck == 0){?>
    	<div style="float:left; margin: auto; margin:50px 0px 0px 200px; ">
        	<span style="font: bold 20px Arial;">亲，没有权限访问。 </span>
        </div>
    <?php }
    else{?>
    	<div class="content-wrapper" >
			<div style="width: 100%; float:left;">
			    <div class="div-input-group" style="float: left;margin-left:20px; margin-bottom:10px;">
			    	<LABEL class="lb_choose_title">筛选时间：</LABEL>
					<input id="statistics_startdate" class="eagle-form-control" type="text" placeholder="'付款日期从 此日期后" 
						value="<?= (empty($_GET['start_date'])?"":$_GET['start_date']);?>" style="width:120px;margin:0px;height:28px;float:left; margin-right:0px;"/>
					<LABEL class="lb_choose_title"> ~ </LABEL>
					<input id="statistics_enddate" class="eagle-form-control" type="text" placeholder="至 此日期前" 
						value="<?= (empty($_GET['end_date'])?"":$_GET['end_date']);?>" style="width:120px;margin:0px;height:28px;float:left; margin-right:20px;"/>
					
		  			<LABEL class="lb_choose_title">币种：</LABEL>
			        <SELECT id="select_currency" class="eagle-form-control" style="float:left; width:60px;margin:0px 30px 0px 0px;">
			        	<OPTION value="RMB" >RMB</OPTION>
			            <OPTION value="USD" >USD</OPTION>
		  			</SELECT>
		  			
		  			<LABEL class="lb_choose_title">模糊搜索：</LABEL>
		  			<SELECT id="select_choose_type" class="eagle-form-control" style="float:left; width:80px;margin:0px;">
			            <OPTION value="sku" >SKU</OPTION>
		  				<OPTION value="name" >商品名称</OPTION>
		  			</SELECT>
		  			<input id="select_choose_value" class="eagle-form-control" type="text" placeholder="请输入  SKU" title="根据输入的SKU查询"
		    			style="width:200px;margin:0px 30px 0px 5px;height:28px;float:left;"/>
		
					<button id="details_search" class="iv-btn btn-search btn-spacing-middle">搜索</button>
					<button type="button" class="iv-btn btn-primary" onclick="product_details.exportExecl()" style="border-style: none; margin-left: 20px;">导出</button>
				</div>
				<div class="div_choose">
				    <span>排序方式：</span>
				    <div style="width: 90%; float:left;">
			  		    <?php 
			  		    $sort = empty($_GET['sort']) ? 'sku' : $_GET['sort'];
			  		    foreach($sort_list as $k => $v){?>	
			      		    <a class="<?= $sort == $k ? 'lb_sort' : '' ?> lb_check_node" value="<?=$k ?>" sorttype="<?= empty($_GET['ordersorttype']) ? '' : 'desc' ?>" onclick="product_details.refresh_sort(this)"><?= $v ?>
			      		    <?= $sort == $k ? '<span class="glyphicon glyphicon-sort-by-attributes'.(empty($_GET['ordersorttype']) ? '' : '-alt').'"></span>' : ''?>
			      		    </a>
						<?php } ?>
					</div>
				</div>
			</div>
				
			<!-- table -->
			<div class="shoplist" style="float:left; width: 90%">
			    <table id="sales_table" cellspacing="0" cellpadding="0" class="table table-hover" style="float:left;font-size: 12px; ">
					<tr class="sales_title">
						<th style="min-width:80px;">图片</th>
						<th style="min-width:200px;">SKU</th>
						<th style="min-width:250px;">商品名称</th>
						<th style="min-width:60px;">订单总量</th>
						<th style="min-width:60px;">销售总量</th>
						<th style="min-width:120px;">销售金额</th>
						<th style="min-width:60px;">采购数量</th>
						<th style="min-width:100px;">采购金额</th>
						<th style="min-width:60px;">库存数量</th>
						<th style="min-width:60px;">在途数量</th>
					</tr>
			        
			    </table>
			    <!-- Modal -->
				<div id="checkOrder" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			    <div class="modal-dialog">
			        <div class="modal-content">
			        </div><!-- /.modal-content -->
			    </div>
			    </div>
			    <!-- /.modal-dialog -->
			</div>
			<div style="width: 100%; float: left;">
			    <div id="statistics_pager_group" >
			    </div>
			</div>
			<div>
			    <input type="hidden" id="choose_start_date" value="" />
			    <input type="hidden" id="choose_end_date" value="" />
			    <input type="hidden" id="choose_currency" value="" />
			    <input type="hidden" id="choose_type" value="" />
			    <input type="hidden" id="choose_value" value="" />
			    <input type="hidden" id="search_count" value="" />
			</div>
		</div>
		
		<div style="font: 0px/0px sans-serif;clear: both;display: block"> </div> 
    <?php }?>
</div>

