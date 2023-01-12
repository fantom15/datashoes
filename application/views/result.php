<div class="col-md-8 offset-md-2">
    <div class="row">
        <div class="col-12" style="text-align: center">
            <img src="assets/img/result/<?= $image ?>" style="width: 100%">
        </div>
        <div class="col-6 pt-3">
            <p>
                I remember
                <?= $user_input->row()->input_one ?>
                in my
                <?= $user_input->row()->input_two ?>
                <?= $user_input->row()->input_three ?>
                <?= $user_input->row()->input_four ?>
                and feeling a bit
                <?= $user_input->row()->input_five ?>
                but mostly <?php $user_input->row()->input_six ?>
                in
                <?= $user_input->row()->input_seven ?>.
            </p>
        </div>
        <div class="col-6 pt-3" style="text-align: center;">
            <form method="POST">
                <input type="hidden" name="again" value="again">
                <button class="btn-start white" style="padding: 5px 30px">Go Again!</button>
            </form>

        </div>
    </div>
</div>