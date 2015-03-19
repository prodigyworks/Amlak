<?php
	require_once("system-header.php");
?>
<h2>Difference Report</h2>
<br>
<form id="reportform" class="reportform" name="reportform" method="POST" action="differencereportpdf.php" target="_new">
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
				Mode
			</td>
			<td>
				<SELECT id="mode" name="mode">
					<OPTION value="PDF">PDF</OPTION>
					<OPTION value="Excel">Excel</OPTION>
				</SELECT>
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

		if ($("#mode").val() == "PDF") {
			$('#reportform').attr("action", "differencereportpdf.php");
			
		} else {
			$('#reportform').attr("action", "differencereportexcel.php");
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
