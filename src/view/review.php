<script type="text/javascript">
	function change(id) {
		var cname = document.getElementById(id).className;
		var ab = document.getElementById(id + "_hidden").value;
		document.getElementById(cname + "rating").innerHTML = ab;

		for (var i = ab; i >= 1; i--) {
			document.getElementById(cname + i).src = "../images/star2.png";
		}
		var id = parseInt(ab) + 1;
		for (var j = id; j <= 5; j++) {
			document.getElementById(cname + j).src = "../images/star1.png";
		}
	}
</script>

<section id="contact">
	<div class="container">
		<div class="title">
			<h1>Tell us your thoughts about O'Kwiz !!</h1>
		</div>
		<form method="post" action="_review.php">
			<div>
				<textarea name="comment" placeholder="Write your comment"></textarea>
				<div class="star">
					<input type="hidden" id="php1_hidden" value=1>
					<img src="../images/star1.png" onmouseover="change(this.id);" id="php1" class="php">
					<input type="hidden" id="php2_hidden" value=2>
					<img src="../images/star1.png" onmouseover="change(this.id);" id="php2" class="php">
					<input type="hidden" id="php3_hidden" value=3>
					<img src="../images/star1.png" onmouseover="change(this.id);" id="php3" class="php">
					<input type="hidden" id="php4_hidden" value=4>
					<img src="../images/star1.png" onmouseover="change(this.id);" id="php4" class="php">
					<input type="hidden" id="php5_hidden" value=5>
					<img src="../images/star1.png" onmouseover="change(this.id);" id="php5" class="php">
				</div>
				<input type="hidden" name="rating" id="phprating" value=0>



				<button type="submit" name="add">Add my review</button>
			</div>
		</form>
		<table>
			<h2>User's feedback</h2>
			<?php
			foreach ($data as $review) {
			?>
				<tr>
					<?php echo htmlentities($review['pseudo']) ?> :
					<?php echo htmlentities($review['appscore']) ?> / 5,
					<?php echo htmlentities($review['review']) ?>
				</tr>
			<?php
			}
			?>
		</table>
	</div>