<?php
	require_once("system-header.php");
?>
<h2>Deliveries Chart</h2>
<br>
<form id="reportform" class="reportform" name="reportform" method="POST" action="chartdeliveries.php" target="_new">
	<table>
		<tr>
			<td>
				Date From
			</td>
			<td>
				<input class="datepicker"  id="datefrom" name="datefrom" required="true" />
			</td>
		</tr>
		<tr>
			<td>
				Date To
			</td>
			<td>
				<input class="datepicker"  id="dateto" name="dateto" required="true" />
			</td>
		</tr>
		<tr>
			<td>
				Truck
			</td>
			<td>
				<?php createCombo("truckid", "id", "name", "{$_SESSION['DB_PREFIX']}truck")?>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<a class="link1" href="javascript: runreport();"><em><b>Run Report</b></em></a>
			</td>
		</tr>
	</table>
</form>
<script>
	function runreport(e) {
		if (! verifyStandardForm("#reportform")) {
			return false;
		}
		$('#reportform').submit();

		try {
			e.preventDefault();

		} catch (e) {

		}
	}
</script>
<?php
	require_once("system-footer.php");
?>
