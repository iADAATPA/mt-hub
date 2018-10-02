<?php include_once '../functions.php'; ?>

    <div class="modal-help">
        <p>
            In the <b>Engines</b> tab, you can create and manage your engines.
        </p>

        <ul>
            <li><b>New</b><br>Click <b>New</b> to add a new engine.
            </li>

            <li><b>Delete</b><br>Click <b>Delete</b> to permanently remove a selected engine. Select
                several engines for deletion by clicking on the checkbox adjacent to it's name.
            </li>

            <li><b>Copy</b><br>Use <b>Copy</b> to make a copy of the highlighted/active engine.</li>

            <li>
                <b>Edit Properties</b><br>
                Use Edit Properties to change how your engine is configured. You can change
                the engine name, language pair or the domain name.
            </li>

        </ul>

    </div>

<?php Helper::makeScrollable('.modal-help') ?>

