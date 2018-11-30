<?php include_once '../functions.php'; ?>

<div class="modal-help">
    <p>
        The All Domains page list all available domains for your account. From here you can manage all the domains by
        crating new ones or deleting existing domains.
    </p>
    <p>
        To create a new Domain click on the <b>Add</b> button and in the modal box enter the domain name. Next select
        the source language and click <b>Save</b>.
    </p>
    <p>
        When the new domain is created, you need to upload some sample data to it. Click the <b>Add Data</b> icon for
        the new domain and paste the data.
        We recommend at least 4,000 segments for each domain.
        The data will be used to determine the domain by the <b>Detect Domain</b> functionality. If you need to modify
        the data, firstly delete the existing data and uphold a new one.
    </p>
</div>

<?php Helper::makeScrollable('.modal-help') ?>

