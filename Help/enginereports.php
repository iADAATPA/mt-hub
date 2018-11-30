<?php include_once '../functions.php'; ?>

<div class="modal-help">
    <p>
        This report includes a number of quality indicators. You can filter by language pair to compare different
        engines; compare the various quality indicators and word counts and see how many words each engine has
        translated. You can also download this report in Excel format.
    </p>
    <p>
        Sample indicators:
        <ul>
            <li>
                BLEU Score (60% or higher indicates better quality output).
            </li>
            <li>
                F-Measure Score (70% or more indicates reduced post-editing effort).
            </li>
            <li>
                TER Score (The lower this score the better).
            </li>
            <li>
                TWC (Total Word Count related to a Training Data used to build the engine).
            </li>
        </ul>
    </p>
</div>

<?php Helper::makeScrollable('.modal-help') ?>

