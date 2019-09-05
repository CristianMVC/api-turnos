<?php
namespace ApiV1Bundle\Tests\Validator;

use ApiV1Bundle\Entity\Validator\SNTValidator;

/**
 * Class SNTValidatorTest
 * @package ApiV1Bundle\Tests\Validator
 */
class SNTValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->validator = new SNTValidator();
    }

    /**
     * Test de validación para campos requeridos
     */
    public function testRequired()
    {
        $errors = $this->validator->validar(['valid' => 'ipsum'], ['valid' => 'required']);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validación para campos requeridos
     */
    public function testRequiredNovalid()
    {
        $errors = $this->validator->validar(['novalid' => ''], ['novalid' => 'required']);
        $this->assertEquals(count($errors), 1);
        $this->assertContains('Novalid', $errors[0]);
    }

    /**
     * Test de validación de numeros enteros
     */
    public function testInteger()
    {
        $errors = $this->validator->validar(['valid' => 1], ['valid' => 'integer']);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validación de numeros enteros
     */
    public function testIntegerNovalid()
    {
        $errors = $this->validator->validar(['novalid' => 'saraza'], ['novalid' => 'integer']);
        $this->assertEquals(count($errors), 1);
        $this->assertContains('Novalid', $errors[0]);
    }

    /**
     * Test de validación de números
     */
    public function testNumeric()
    {
        $errors = $this->validator->validar(['valid' => 1], ['valid' => 'numeric']);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validación de números
     */
    public function testNumericNovalid()
    {
        $errors = $this->validator->validar(['novalid' => 'saraza'], ['novalid' => 'numeric']);
        $this->assertEquals(count($errors), 1);
        $this->assertContains('Novalid', $errors[0]);
    }

    /**
     * Test de validacion de numeros decimales
     */
    public function testFloat()
    {
        $errors = $this->validator->validar(['valid' => 2.1], ['valid' => 'float']);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validacion de numeros decimales
     */
    public function testFloatNovalid()
    {
        $errors = $this->validator->validar(['novalid' => 'a'], ['novalid' => 'float']);
        $this->assertEquals(count($errors), 1);
        $this->assertContains('Novalid', $errors[0]);
    }

    /**
     * Test de validacion de emails
     */
    public function testEmail()
    {
        $errors = $this->validator->validar(['valid' => 'me@nowhere.com'], ['valid' => 'email']);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validacion de emails
     */
    public function testEmailNovalid()
    {
        $errors = $this->validator->validar(['novalid' => 'saraza'], ['novalid' => 'email']);
        $this->assertEquals(count($errors), 1);
        $this->assertContains('Novalid', $errors[0]);
    }

    /**
     * Test de validacion de fechas
     */
    public function testDate()
    {
        $errors = $this->validator->validar(['valid' => '2017-01-01'], ['valid' => 'date']);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validacion de fechas
     */
    public function testDateNovalid()
    {
        $errors = $this->validator->validar(['novalid' => 'saraza'], ['novalid' => 'date']);
        $this->assertEquals(count($errors), 1);
        $this->assertContains('Novalid', $errors[0]);
    }

    /**
     * Test de validacion de cuil
     */
    public function testCuil()
    {
        $errors = $this->validator->validar([
            'validA' => '20-93941676-6',
            'validB' => '20939416766',
            'validC' => '23-28423371-9'
        ], [
            'validA' => 'cuil',
            'validB' => 'cuil',
            'validC' => 'cuil'
        ]);
        $this->assertEquals(count($errors), 0);
    }

    /**
     * Test de validacion de cuil
     */
    public function testCuilNovalid()
    {
        $errors = $this->validator->validar([
            'novalidA' => '20-20564897-6',
            'novalidB' => '20-4857967-7',
            'novalidC' => '23456',
            'novalidD' => 'asdf1234',
            'novalidE' => '1234asdf'
        ], [
            'novalidA' => 'cuil',
            'novalidB' => 'cuil',
            'novalidC' => 'cuil',
            'novalidD' => 'cuil',
            'novalidE' => 'cuil'
        ]);
        $this->assertEquals(count($errors), 5);
        foreach ($errors as $error) {
            $this->assertContains('Novalid', $error);
        }
    }
}
