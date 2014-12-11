<?php

    Router::connect('/abn_lookup/:abn', array('controller' => 'AbnLookup', 'action' => 'abn', 'plugin' => 'AbnLookup'));