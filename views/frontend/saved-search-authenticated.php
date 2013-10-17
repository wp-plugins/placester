<div style="display:none">
	<form  method="post" id="pl_saved_search_register_form" action="#<?php // echo $role; ?>" name="pl_saved_search_register_form" class="saved-search-wrapper" autocomplete="off">

        <div style="display:none" class="success" id="saved_search_message">Search has been saved!</div>

        <h2 style="margin-bottom: 10px">Saved Search</h2>

        <p style="color: black">Please give your search a name.</p>

        <div id="pl_saved_search_register_form_inner_wrapper">
          	<p class="reg_form_pass" style="margin-bottom: 10px; float: left; clear:both">
                <label for="user_password">Name of the Search</label>
                <input style="width: 250px" type="text" tabindex="26" size="20" required="required" class="input" id="user_search_name" name="user_search_name" data-message="Name your search" placeholder="Name your Search">
            </p>

            <div id="saved_search_value_wrapper">
            	<h3>Your Search</h3>
            	<div id="saved_search_values">
            		<ul>
            			<li>All Listings</li>
            		</ul>
            	</div>
            </div>

            <p class="reg_form_submit" style="float: right; clear: both;">
                <input type="submit" tabindex="28" class="submit button-primary" value="Save" id="pl_submit" name="pl_register">
            </p>
        </div>		
    </form>
</div>