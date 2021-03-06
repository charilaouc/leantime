<?php
defined( 'RESTRICTED' ) or die( 'Restricted access' );


$helper = $this->get('helper');
?>
<script type="text/javascript">
jQuery(document).ready(function(){

 	var startDate;
    var endDate;

    var selectCurrentWeek = function () {
        window.setTimeout(function () {
            jQuery('.ui-weekpicker').find('.ui-datepicker-current-day a').addClass('ui-state-active').removeClass('ui-state-default');
        }, 1);
    };

    var setDates = function (input) {
        var $input = jQuery(input);
        var date = $input.datepicker('getDate');
        if (date !== null) {
            var firstDay = $input.datepicker( "option", "firstDay" );
            var dayAdjustment = date.getDay() - firstDay;
            if (dayAdjustment < 0) {
                dayAdjustment += 7;
            }
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - dayAdjustment);
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - dayAdjustment + 6);
    
            var inst = $input.data('datepicker');
            var dateFormat = inst.settings.dateFormat || jQuery.datepicker._defaults.dateFormat;
            jQuery('#startDate').val(jQuery.datepicker.formatDate(dateFormat, startDate, inst.settings));
            jQuery('#endDate').val(jQuery.datepicker.formatDate(dateFormat, endDate, inst.settings));
        }
    };



    jQuery('.week-picker').datepicker({
    	firstDay: 1,
        beforeShow: function () {
            jQuery('#ui-datepicker-div').addClass('ui-weekpicker');
            selectCurrentWeek();
        },
        onClose: function () {
            jQuery('#ui-datepicker-div').removeClass('ui-weekpicker');
        },
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function (dateText, inst) {
            setDates(this);
            selectCurrentWeek();
            jQuery(this).change();
            jQuery("#timesheetList").submit();
        },
        beforeShowDay: function (date) {
            var cssClass = '';
            if (date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';
            return [true, cssClass];
        },
        onChangeMonthYear: function (year, month, inst) {
            selectCurrentWeek();
        }
    });
    
    setDates('.week-picker');

    var $calendarTR = jQuery('.ui-weekpicker .ui-datepicker-calendar tr');
    $calendarTR.live('mousemove', function () {
        jQuery(this).find('td a').addClass('ui-state-hover');
    });
    $calendarTR.live('mouseleave', function () {
        jQuery(this).find('td a').removeClass('ui-state-hover');
    });

	jQuery("#startDate").datepicker("setDate", new Date(<?php echo $this->get("dateFrom")->format('Y, m-1, d'); ?>));
	jQuery("#endDate").datepicker("setDate", new Date(<?php echo $this->get("dateFrom")->add(new DateInterval('P6D'))->format('Y, m-1, d'); ?>));
	
	<?php $this->get("dateFrom")->sub(new DateInterval('P6D')); ?>
	
    jQuery(".project-select").chosen();
    jQuery(".ticket-select").chosen();
    
    jQuery(".project-select").change(function(){

            jQuery(".ticket-select").removeAttr("selected");
            jQuery(".ticket-select").val("");
            jQuery(".ticket-select").trigger("liszt:updated");

    		jQuery(".ticket-select option").show();
    		jQuery("#ticketSelect .chzn-results li").show();
    		var selectedValue = jQuery(this).find("option:selected").val();
    		jQuery("#ticketSelect .chzn-results li").not(".project_"+selectedValue).hide();


    });

    jQuery(".ticket-select").change(function() {
        console.log("asdf");
        var selectedValue = jQuery(this).find("option:selected").attr("data-value");
        jQuery(".project-select option[value="+selectedValue+"]").attr("selected", "selected");
        jQuery(".project-select").trigger("liszt:updated");
    });

    jQuery("#nextWeek").click(function() {

        var date = jQuery("#endDate").datepicker('getDate');
        var endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 7);
        var startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1);

        var inst = jQuery("#endDate").data('datepicker');
        var dateFormat = inst.settings.dateFormat || jQuery.datepicker._defaults.dateFormat;
        jQuery('#startDate').val(jQuery.datepicker.formatDate(dateFormat, startDate, inst.settings));
        jQuery('#endDate').val(jQuery.datepicker.formatDate(dateFormat, endDate, inst.settings));
        jQuery("#timesheetList").submit();

    });

    jQuery("#prevWeek").click(function() {

        var date = jQuery("#startDate").datepicker('getDate');
        var endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 1);
        var startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 7);

        var inst = jQuery("#startDate").data('datepicker');
        var dateFormat = inst.settings.dateFormat || jQuery.datepicker._defaults.dateFormat;
        jQuery('#startDate').val(jQuery.datepicker.formatDate(dateFormat, startDate, inst.settings));
        jQuery('#endDate').val(jQuery.datepicker.formatDate(dateFormat, endDate, inst.settings));
        jQuery("#timesheetList").submit();

    });
    
    jQuery(".timesheetTable input").change(function(){
    	
    	//Row Sum
    	colSumMo = 0;
    	colSumTu = 0;
    	colSumWe = 0;
    	colSumTh = 0;
    	colSumFr = 0;
    	colSumSa = 0;
    	colSumSu = 0;
    	
    	
    	jQuery(".timesheetRow").each(function(i){
    		var rowSum = 0;
    		jQuery(this).find("input.hourCell").each(function(){
    			var currentValue = parseInt(jQuery(this).val());
    			rowSum = rowSum + currentValue;
    			
    			var currentClass = jQuery(this).parent().attr('class');
    			
    			switch(currentClass){
    				case "rowMo": colSumMo = colSumMo + currentValue; break;
    				case "rowTu": colSumTu = colSumTu + currentValue; break;
    				case "rowWe": colSumWe = colSumWe + currentValue; break;
    				case "rowTh": colSumTh = colSumTh + currentValue; break;
    				case "rowFr": colSumFr = colSumFr + currentValue; break;
    				case "rowSa": colSumSa = colSumSa + currentValue; break;
    				case "rowSu": colSumSu = colSumSu + currentValue; break;
    			}
    			
    		});
    		jQuery(this).find(".rowSum strong").text(rowSum);
    	});
    	
    	jQuery("#sumMo").text(colSumMo);
    	jQuery("#sumTu").text(colSumTu);
    	jQuery("#sumWe").text(colSumWe);
    	jQuery("#sumTh").text(colSumTh);
    	jQuery("#sumFr").text(colSumFr);
    	jQuery("#sumSa").text(colSumSa);
    	jQuery("#sumSu").text(colSumSu);
    	
    	var finalSum = colSumMo + colSumTu + colSumWe + colSumTh + colSumFr + colSumSa + colSumSu;
    	
    	jQuery("#finalSum").text(finalSum);
    	
    });

    <?php if(isset($_SESSION['userdata']['settings']["modals"]["mytimesheets"]) === false || $_SESSION['userdata']['settings']["modals"]["mytimesheets"] == 0){     ?>
    leantime.helperController.showHelperModal("mytimesheets");
    <?php
    //Only show once per session
    $_SESSION['userdata']['settings']["modals"]["mytimesheets"] = 1;
    } ?>
 });
    
</script>


<div class="pageheader">

            <div class="pageicon"><span class="iconfa-time"></span></div>
            <div class="pagetitle">
                <h5><?php echo $this->__('headline.overview'); ?></h5>
                <h1><?php echo $this->__('headline.my_timesheets'); ?></h1>
            </div>
        </div><!--pageheader-->
        
        <div class="maincontent">
            <div class="maincontentinner">
                <?php
                echo $this->displayNotification();
                ?>

<form action="<?=BASE_URL ?>/timesheets/showMy" method="post" id="timesheetList">

<div class="headtitle" style="margin:0px; background: #eee;">
	<h4 class="widgettitle title-primary"><?php echo $this->__('headline.my_timesheets'); ?></h4>
	<div class="padding10">
		<span><?php echo $this->__('label.week_from')?></span>
        <a href="javascript:void(0)" style="font-size:16px;" id="prevWeek"><i class="fa fa-chevron-left"></i></a>
		<input type="text" class="week-picker" name="startDate" id="startDate" placeholder="<?php echo $this->__('language.jsdateformat')?>" value="" style="margin-top:5px;"/> <?php echo $this->__('label.until'); ?>
		<input type="text" class="week-picker" name="endDate" id="endDate" placeholder="<?php echo $this->__('language.jsdateformat')?>" style="margin-top:6px;"/>
        <a href="javascript:void(0)" style="font-size:16px;" id="nextWeek"><i class="fa fa-chevron-right"></i></a>
        <input type="hidden" name="search" value="1" />


	</div>
	
</div>
<table cellpadding="0" width="100%" class="table table-bordered display timesheetTable" id="dyntableX">
	<colgroup>
      	  <col class="con0" >
          <col class="con1" >
      	  <col class="con0" >
          <col class="con1" >
      	  <col class="con0" >
          <col class="con1" >
      	  <col class="con0" >
          <col class="con1" >
      	  <col class="con0" >
          <col class="con1">
          <col class="con0">
	</colgroup>
	<thead>
    <?php

    $dateFromHeader = clone $this->get("dateFrom");
    $currentDate = $dateFromHeader->format($this->__('language.dateformat'));
    $days = explode(',', $this->__('language.dayNamesShort'));
    $today = date($this->__('language.dateformat'));

    ?>
	<tr>
        <th><?php echo $this->__('label.client_product')?></th>
		<th><?php echo $this->__('subtitles.todo')?></th>
		<th><?php echo $this->__('label.type')?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[1]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[2]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[3]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[4]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[5]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[6]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $days[0]?><br /><?php echo $currentDate; $currentDate = $dateFromHeader->add(new DateInterval('P1D'))->format($this->__('language.dateformat')); ?></th>
		<th class="<?php if($today == $currentDate)echo"active"; ?>"><?php echo $this->__('label.total')?></th>
	</tr>
	</thead>
	<tbody>
		<?php 
			$sumMon = 0;
			$sumTu = 0;
			$sumWe = 0;
			$sumTh = 0;
			$sumFr = 0;
			$sumSa = 0;
			$sumSu = 0;

        $today = date($this->__('Y-m-d'));
		foreach($this->get('allTimesheets') as $timeRow){
			
			$sumMon = $timeRow["hoursMonday"] + $sumMon;
			$sumTu = $timeRow["hoursTuesday"] + $sumTu;
			$sumWe = $timeRow["hoursWednesday"] + $sumWe;
			$sumTh = $timeRow["hoursThursday"] + $sumTh;
			$sumFr = $timeRow["hoursFriday"] + $sumFr;
			$sumSa = $timeRow["hoursSaturday"] + $sumSa;
			$sumSu = $timeRow["hoursSunday"] + $sumSu;

			$dateFrom = clone $this->get("dateFrom");
			
			$timesheetId = "new";


									
			$workDatesArray = explode(",", $timeRow["workDates"]);
			
			$rowSum = $timeRow["hoursMonday"] + $timeRow["hoursTuesday"] + $timeRow["hoursWednesday"] + $timeRow["hoursThursday"] + $timeRow["hoursFriday"] + $timeRow["hoursSaturday"] + $timeRow["hoursSunday"];
			
		?>
			
			<tr class="gradeA timesheetRow">
				<td width="14%"><?php echo $timeRow["name"]; ?></td>
				<td width="14%"><?php echo $timeRow["headline"]; ?></td>
				<td width="10%"><?php echo $this->__($this->get('kind')[$timeRow['kind']]); ?></td>
				<?php $currentDate = $dateFrom->format('Y-m-d'); ?>
				<td width="7%" class="rowMo <?php if($today == $currentDate)echo"active"; ?>" <?php if($today == $currentDate)echo"active"; ?>><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursMonday"]; ?>" /></td>
				<?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
				<td width="7%" class="rowTu <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate; ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursTuesday"]; ?>" /></td>
				<?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
				<td width="7%" class="rowWe <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate; ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursWednesday"]; ?>" /></td>
				<?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
				<td width="7%" class="rowTh <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate; ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursThursday"]; ?>" /></td>
				<?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
				<td width="7%" class="rowFr <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate; ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursFriday"]; ?>" /></td>
				<?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
				<td width="7%" class="rowSa <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate; ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursSaturday"]; ?>" /></td>
				<?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
				<td width="7%" class="rowSu <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="<?php echo $timeRow["workDates"]; ?> hourCell" name="<?php echo $timeRow["ticketId"];?>|<?php if(in_array($currentDate, $workDatesArray) == true) echo "existing"; else echo "new";?>|<?php echo $currentDate; ?>|<?php echo $timeRow["kind"];?>" value="<?php echo $timeRow["hoursSunday"]; ?>" /></td>
				<td width="7%" class="rowSum <?php if($today == $currentDate)echo"active"; ?>"><strong><?php echo $rowSum; ?></strong></td>
			</tr>
			
		<?php } ?>
		<?php
			$dateFrom = clone $this->get("dateFrom"); 
		?>
			<tr class="gradeA timesheetRow">
				<td width="14%">
					<div class="form-group">                    
                    	<select data-placeholder="<?php echo $this->__('input.placeholders.choose_project')?>" style="" class="project-select" >
                         	<option value=""></option> 
                            <?php foreach($this->get('allProjects') as $projectRow){ ?>
                            	<?php echo"<option value=".$projectRow["id"].">".$this->escape($projectRow["clientName"])." / ".$this->escape($projectRow["name"])."</option>"; ?>
                            <?php }?>
                        </select>
                    </div>
				</td>
				<td width="14%">
					<div class="form-group" id="ticketSelect">                    
                    	<select data-placeholder="<?php echo $this->__('input.placeholders.choose_todo')?>" style="" class="ticket-select" name="ticketId">
                         	<option value=""></option> 
                            <?php foreach($this->get('allTickets') as $ticketRow){ ?>
                            	<?php echo"<option value=".$ticketRow["id"]." data-value='".$ticketRow["projectId"]."' class='project_".$ticketRow["projectId"]."'>".$ticketRow["id"]." ".$this->escape($ticketRow["headline"])."</option>"; ?>
                            <?php }?>
                        </select>
                    </div>
				</td>
				<td width="14%">
					<select class="kind-select" name="kindId">
                            <?php foreach($this->get('kind') as $key => $kindRow){ ?>
                            	<?php echo"<option value=".$key.">".$this->__($kindRow)."</option>"; ?>
                            <?php }?>
                        </select>
				</td>
                <?php $currentDate = $dateFrom->format('Y-m-d');?>
				<td width="7%" class="rowMo <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
                <?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
                <td width="7%" class="rowTu <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
                <?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
                <td width="7%" class="rowWe <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
                <?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
                <td width="7%" class="rowTh <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
                <?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
                <td width="7%" class="rowFr <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
                <?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
                <td width="7%" class="rowSa <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
                <?php $currentDate = $dateFrom->add(new DateInterval('P1D'))->format('Y-m-d'); ?>
                <td width="7%" class="rowSu <?php if($today == $currentDate)echo"active"; ?>"><input type="text" class="hourCell" name="new|new|<?php  echo $currentDate ?>|GENERAL_BILLABLE" value="0" /></td>
				<td width="7%" class="rowSum "><strong>0</strong></td>
			</tr>
	</tbody>
		
	<tfoot>
		<tr style="font-weight:bold;">
			<td colspan="3"><?php echo $this->__('label.total')?></td>
			<td id="sumMo"><?php echo $sumMon; ?></td>
			<td id="sumTu"><?php echo $sumTu; ?></td>
			<td id="sumWe"><?php echo $sumWe; ?></td>
			<td id="sumTh"><?php echo $sumTh; ?></td>
			<td id="sumFr"><?php echo $sumFr; ?></td>
			<td id="sumSa"><?php echo $sumSa; ?></td>
			<td id="sumSu"><?php echo $sumSu; ?></td>
			<td id="finalSum"><?php echo ($sumMon + $sumTu + $sumWe + $sumTh + $sumFr + $sumSa + $sumSu); ?></td>
		</tr>
	</tfoot>
</table>
<div class="right">
	<input type="submit" name="saveTimeSheet" value="Save"/>
</div>

<?php /*

		
<table class='table table-bordered' cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="dyntable2">
		<colgroup>
      	  <col class="con0"/>
          <col class="con1" />
      	  <col class="con0"/>
          <col class="con1" />
      	  <col class="con0"/>
          <col class="con1" />
      	  <col class="con0"/>
          <col class="con1" />
      	  <col class="con0"/>
          <col class="con1" />
      	  <col class="con0"/>
          <col class="con1" />
	</colgroup>
	<thead>
		<tr>
			<th><?php echo $lang['ID']; ?></th>
			<th><?php echo $lang['DATE']; ?></th>
			<th><?php echo $lang['HOURS']; ?></th>
			<th><?php echo $lang['BILLABLE_HOURS'] ?></th>
			<th><?php echo $lang['PLANHOURS']; ?></th>
			<th><?php echo $lang['DIFFERENCE_HOURS']; ?></th>
			<th><?php echo $lang['TICKET']; ?></th>
			<th><?php echo $lang['PROJECT']; ?></th>
			<th><?php echo $lang['KIND']; ?></th>
			<th><?php echo $lang['DESCRIPTION']; ?></th>
			
		</tr>
		
	</thead>
	<tbody>

	<?php
	$sum = 0;
	$billableSum = 0;
	foreach($this->get('allTimesheets') as $row) {
		$sum = $sum + $row['hours'];?>
		<tr>
			<td>
				<a href="<?=BASE_URL ?>/timesheets/editTime/<?php echo $row['id']; ?>">#<?php echo $row['id']; ?></a>
			</td>
			<td><?php echo $helper->timestamp2date($row['workDate'], 2); ?></td>
			<td><?php echo $row['hours']; ?></td>
			<td>
			<?php 
				if ($row['kind'] != 'GENERAL_NOT_BILLABLE' && $row['kind'] != 'BUGFIXING_NOT_BILLABLE') {
					echo $row['hours'];
					$billableSum += $row['hours'];
				}
			?>
			</td>
			<td><?php echo $row['planHours']; ?></td>
			<?php $diff = $row['planHours']-$row['hours']; ?>
			<td <?php if($diff<0)echo'class="new" ';?>><?php echo $diff; ?></td>
			<td><a href="<?=BASE_URL ?>/tickets/showTicket/<?php echo $row['ticketId']; ?>"><?php echo $row['headline']; ?></a></td>
			<td><a href="<?=BASE_URL ?>/projects/showProject/<?php echo $row['projectId']; ?>"><?php echo $row['name']; ?></a></td>
			<td><?php echo $lang[$row['kind']]; ?></td>
			<td><?php echo $row['description']; ?></td>
			
		</tr>
		<?php } ?>
		<?php if(count($this->get('allTimesheets')) === 0){ ?>
		<tr>
			<td colspan="12"><?php echo $lang['NO_RESULTS']; ?></td>
		</tr>

		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2"><strong><?php echo $lang['ALL_HOURS']; ?>:</strong></td>
			<td colspan="1"><strong><?php echo $sum; ?></strong></td>
			<td colspan="7"><strong><?php echo $billableSum; ?></strong></td>
			
			</td></td>
		</tr>
	</tfoot>
</table>

</div>*/?>
</form>

					</div>
				</div>
