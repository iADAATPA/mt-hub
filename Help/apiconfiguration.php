<?php include_once '../functions.php'; ?>

    <div class="modal-help">
        <h4>URL End Point</h4>
        <p>
            Specify an API endpoint for your API methods, eg. https://app.kantanmt.com/api/translate.
        </p>

        <h4>Type</h4>
        <p>
            Right now for the simplicity of configuration the MT-Hub platform only supports POST requests.
        </p>

        <h4>Authorization</h4>
        <p>
            Select one of the authorization methods if needed. When any of them selected, please make sure a Customer
            username and password are set on the Consumer page.
        </p>

        <h4>Request</h4>
        <p>
            Please provide a valid JSON with your request body. To every key you can assign following values that will be mapped from MT-Hub request:
            <ul>
            <li><code>token</code> - authorization token.</li>
            <li><code>segments</code> - segments to translate.</li>
            <li><code>source</code> - source language.</li>
            <li><code>target</code> - target language.</li>
            <li><code>domain</code> - domain name.</li>
            <li><code>userName</code> - user name.</li>
            <li><code>password</code> - password.</li>
            <li><code>file</code> - base64 encoded file content.</li>
            <li><code>fileType</code> - file type.</li>
            <li><code>guId</code> - globally unique identifier.</li>
            <li><code>engineName</code> - a selected for the request Engine Name value will be send in the POST request.</li>
            <li><code>engineCustomId</code> - a selected for the request Engine Custom Id value will be send in the POST request.</li>
            </ul>
            If your parameter accepts arrays please add at the end of the value <code>[]</code> eg. <code>segments[]</code>.
            In a case of segments, if <code>[]</code>  are not provided at the end of the value name, arrays will be converted to a string.<br/>
            Not listed values will be treated as a string and send unchanged in the POST request.<br/><br/>
            For example request to the KantanMT API translate method that looks like:<br/>
            <code>{"auth":"DSbd663y6gdgdg3#d","segments":["This is a segment for translation"],"src":"en","trg":"fr"}</code><br/>
            will have the following format:<br/>
            <code>{"auth":"token","segments":"segments[]","src":"source","trg":"target"}</code>
        </p>

        <h4>Custom Headers</h4>
        <p>
            If your API expect some data in a header please provide a valid JSON formatted the same way as <b>Request</b>. Otherwise, leave it blank.
        </p>

        <h4>Callback Url Parameters</h4>
        <p>
            For asynchronous methods MT-Hub provides standard callback endpoint. If you wish add some parameters to the endpoint url please provide a valid JSON with the same format as for the <b>Request</b> field.
        </p>

        <h4>Response</h4>
        <p>
            In the response field enter a path to the translated segments/file/guid in your Supplier API response.<br/>
            To create the path use the following mark up:
            <ul>
                <li><code>/</code> - to separate nested array elements.</li>
                <li><code>string</code> - to name the array key.</li>
                <li><code>[]</code> - to describe the translated segments array.</li>
                <li><code>[]/string</code> - to describe the array key for a translated text in the translated segments array.</li>
            </ul>
            For example a Response for the following json:<br/>
            <code>{ "response": { "type": "translation", "body": { "translationData": [ { "src": "this is a segment", "trg": "C'est un segment", "id": 0 }, { "src": "another segment", "trg": "Un autre segment", "id": 1 } ] } } }</code>
            <br/>will have the following format:<br/>
            <code>response/body/translationData/[]/trg</code>
            <br/>and for:<br/>
            <code>["the sun shines bright","the food is pea"]</code>    <br/>
            it will look like:<br/>
            <code>[]</code>
        </p>
        <p>
            If the Response path will not find a match in your response, the requested will be treated as failed and 500 error will be returned to a Consumer.
        </p>
    </div>

<?php Helper::makeScrollable('.modal-help') ?>

