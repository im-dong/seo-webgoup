<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Edit Your Profile</h2>
            <p>Update your public profile information.</p>
            <form action="<?php echo URLROOT; ?>/users/editProfile" method="post" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="username">Username: <sup>*</sup></label>
                    <input type="text" name="username" class="form-control form-control-lg <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username']; ?>" disabled>
                    <small class="form-text text-muted">Username cannot be changed.</small>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>" disabled>
                    <small class="form-text text-muted">Email cannot be changed.</small>
                </div>
                <div class="form-group mb-3">
                    <label for="bio">Bio:</label>
                    <textarea name="bio" class="form-control form-control-lg" rows="5"><?php echo $data['bio']; ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="profile_image">Profile Image:</label>
                    <input type="file" name="profile_image" id="profile_image_input" class="form-control form-control-lg <?php echo (!empty($data['profile_image_err'])) ? 'is-invalid' : ''; ?>">
                    <img id="profile_image_preview" src="<?php echo $data['profile_image_url'] ?? 'https://via.placeholder.com/150'; ?>" alt="Image Preview" class="img-thumbnail mt-2" style="width: 150px; height: auto;">
                    <span class="invalid-feedback"><?php echo $data['profile_image_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="website_url">Website URL:</label>
                    <input type="text" name="website_url" class="form-control form-control-lg <?php echo (!empty($data['website_url_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['website_url']; ?>">
                    <span class="invalid-feedback"><?php echo $data['website_url_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="country">Country:</label>
                    <select name="country" id="country" class="form-select form-select-lg">
                        <?php
                        $countries = [
                            "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan",
                            "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi",
                            "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic of the", "Congo, Republic of the", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic",
                            "Denmark", "Djibouti", "Dominica", "Dominican Republic",
                            "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia",
                            "Fiji", "Finland", "France",
                            "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana",
                            "Haiti", "Honduras", "Hungary",
                            "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy",
                            "Jamaica", "Japan", "Jordan",
                            "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan",
                            "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",

                            "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar",
                            "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway",
                            "Oman",
                            "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal",
                            "Qatar",
                            "Romania", "Russia", "Rwanda",
                            "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria",
                            "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
                            "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan",
                            "Vanuatu", "Vatican City", "Venezuela", "Vietnam",
                            "Yemen",
                            "Zambia", "Zimbabwe"
                        ];
                        $selectedCountry = $data['country'] ?? '';
                        echo '<option value="">-- Select Country --</option>';
                        foreach ($countries as $country) {
                            $selected = ($country == $selectedCountry) ? 'selected' : '';
                            echo "<option value=\"{$country}\" {$selected}>{$country}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="contact_method">Contact Method (Private):</label>
                    <input type="text" name="contact_method" class="form-control form-control-lg" value="<?php echo $data['contact_method'] ?? ''; ?>">
                    <small class="form-text text-muted">This information is private and only visible to administrators.</small>
                </div>
                
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Save Changes" class="btn btn-success btn-block">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupImagePreview('profile_image_input', 'profile_image_preview');
});
</script>