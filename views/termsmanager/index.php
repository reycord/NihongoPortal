
 
<div class="container"> 
  <div class="col-xs-8" style="margin:0 auto; float: inherit;">
    <form style="height: 100px;" enctype="multipart/form-data" id="company-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
 
		<div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 20px;margin-bottom: 5px;">
            <div style="margin-top: 5px;" >
    		    <h4 class="modal-title">利用規約情報</h4> 
    			<table id="regisDataMessage" class="col-xs-12 regisTable" >                 
	                <tr class="">
	                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;"　rowspan="2">インポート情報</th>                 
	                    <td class="form-group"  style="margin: 0;padding-bottom: 2px;padding-top: 2px; margin-left: 5px;display: inline-flex;width: 100%;">
	                        <input  type="file" name="file_import"/>    
	                    </td>   
	                    <td class="form-group"  style="margin: 0;padding-bottom: 2px;padding-top: 2px; margin-left: 5px;display: inline-flex;width: 100%;">
	                        <button style="margin-right: 3px;padding: 1px 15px 2px;" type="submit" class="btn btn-default" name="submit" value="import" id="import" >インポート</button>
	                    </td>              
	                </tr> 
                </table> 

    		
    		</div>
	    </div>
	                
        <div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 20px;">
            <h4 class="modal-title">最新の利用規約の内容</h4>            
            <table id="regisDataMessage" class="col-xs-12 regisTable" >                 
                <tr class="">
                    <td class="form-group"  style="margin: 0;padding-bottom: 2px;padding-top: 2px; width: 100%;">
                        <textarea style="height: 350px;padding:5px 10px; width: 100%; font-size: 13px;" readonly ><?php echo $data['content_terms'] ?></textarea>
                    </td>               
                </tr> 
                  
             </table>
        </div>
        
        <div class="col-xs-12" style="margin-top: 5px;z-index: 999" id="message_div">
            <div id="message_div_error" <?php if ($data['success'] == true): ?> style="display: none; padding: 5px;" <?php endif ?> 
                id="error_div"class="alert alert-warning alert-dismissible  " role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                <strong>エラー： </strong>
                <span id="message"><?php echo $data['message'] ?></span>
            </div>
            
            <?php if ($data['success'] == true && $data['message'] != ""): ?>
                <div id="success_div"class="alert alert-success alert-dismissible " role="alert" style="padding: 5px;">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>アラーム： </strong> <?php echo $data['message'] ?>
                </div>
            <?php endif ?>
        </div> 
        
  </form> 
  
    
</div>

</div>