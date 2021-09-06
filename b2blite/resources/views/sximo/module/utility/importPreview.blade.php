<?php //echo '<pre>';print_r($csv); echo '</pre>'; exit; ?>
<b> Total Rows : </b> <?php echo count($csv); ?>
<hr />
<div class="table-responsive">
<table class="table table-bordered">
	<thead>
		<tr>
		@foreach($table as $tab)
			<th> {{ $tab['field'] }}</th>
		@endforeach
		</tr>

	</thead>
	<tbody>
	<?php 
	$i = 0; 
	foreach($csv as $row) {
		if( ++$i <=5) {
		?>
		<tr>
			<?php foreach($row as $key=>$val) {?>
			<td> <?php print_r($val);?></td>
			<?php } ?>

		</tr>
	<?php } } ?>
	</tbody>
	
</table>
</div>