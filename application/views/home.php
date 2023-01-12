<form method="POST">
	<div class="col-md-8 offset-md-2">
		<div class="row">
			<div class="col-1">
				<img src="assets/img/icons/hand.svg" style="position: absolute">
			</div>
			<div class="col-11">
				<div class="col-12">
					<h1 class="blue" style="font-size: 64px;">Hi there!</h1>
					<p class="blue font-25" style="margin-top: -20px;">
						Let’s talk about your shoes <img src="assets/img/icons/gray-shoe.svg"><img src="assets/img/icons/red-shoe.svg"><img src="assets/img/icons/black-shoe.svg">
					</p>
				</div>
				<div class="col-12 blue font-25">
					<p class="blue">We are trainees from the Digital Society School and we would love to know </p>
					<p>✨ the story of your shoes ✨</p>
				</div>
				<div class="col-12 blue font-25">
					<p>In return, we'll create a personal artwork <img src="assets/img/icons/star.svg" style="width: 25px"> <img src="assets/img/icons/pictur.svg" style="width: 25px"> <img src="assets/img/icons/star.svg" style="width: 25px"></p>
					<input type="email" name="email" id="email-input" required placeholder="email"> <span><?= ($error) ? $error_msg : '' ?></span>
					<p class="white font-12 mt-2">(it’s only to share your personal comic with you, no spam!)</p>
				</div>
				<div class="col-12 pt-3">
					<p class="blue font-25">Interested? Head to the next page</p>
					<button class="btn-start white"> Get started !</button>
				</div>
			</div>
		</div>
	</div>
</form>