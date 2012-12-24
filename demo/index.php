<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pays I/O From Example</title>
</head>
<body>

<?php
require_once 'head.php';

$amount = \Paysio\Currency::normalizeAmount(39.99); // amount in kop.

$form = new \Paysio\Form('paysio');
$form->setParams(array('amount' => $amount));

if (!empty($_POST['payment_system_id'])) {
    $form->setValues($_POST);

    try {
        $params = array(
            'amount' => $amount,
            'description' => 'Test charge',
            'payment_system_id' => $_POST['payment_system_id'],

            'success_url' => 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/success.php',
            'failure_url' => 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/failure.php',
            'return_url' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
        );
        if (isset($_POST['wallet']['account'])) {
            $params['wallet']['account'] = $_POST['wallet']['account'];
        }

        $charge = \Paysio\Charge::create($params);
        $form->addParams(array('charge_id' => $charge->id));
    } catch (\Paysio\Api\Exception\BadRequest $e) {
        $errorParams = $e->getParams();

        $form->setErrors($errorParams);
    }
}
?>

<?php \Paysio\Api::setPublishableKey('pk_7MrhSVEjYq8F1PKEqhAj192fZUV8Ooitl4GQBkL') ?>
<?php $form->render(array('style' => 'width: 510px; margin: 0 auto;')) ?>

</body>
</html>