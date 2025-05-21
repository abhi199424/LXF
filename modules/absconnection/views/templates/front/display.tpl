{extends file='page.tpl'}
{block name='page_content'}
<div class="row main-abs-section">
    <div class="col-md-6 abs-section-left">
        {hook h='displayID1Customhtml9'}
    </div>

    <div class="col-md-6 abs-section-right">
        <div class="login_rgt_content">
            <h2>Créez votre compte ou connectez-vous</h2>
        <form method="post" action="">
            <div class="form-group">
                <!-- <label for="email">Enter your email:</label> -->
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required />
            </div>
            <div class="login_rgt_additional_text">
                <p><strong>Déjà membre de La Team lxfstore.fr?</strong></p>
                <p>Renseignez la même adresse email que celle utilisée <br>
lors de la création de votre carte.</p>
            </div>
            <button type="submit" name="submit_email" class="btn btn-primary mt-2 submit_btn">Continuer</button>
        </form>
    </div>
    </div>
</div>
{/block}
