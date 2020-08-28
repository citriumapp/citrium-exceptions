<?php

namespace Citrium\Exceptions\Tests\Unit;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use PHPUnit\Framework\TestCase;
use Citrium\Exceptions\Exception;
use Ramsey\Uuid\Validator\GenericValidator;
use Symfony\Component\HttpFoundation\Response;

class ExceptionTest extends TestCase
{
    /** @test */
    public function key_details_can_be_returned_for_a_generic_error()
    {
        $exception = new Exception;

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $exception->status());
        $this->assertEquals('unknown_error', $exception->errorCode());
        $this->assertEquals('An error has occurred', $exception->title());
        $this->assertEquals(Exception::GENERIC_ERROR, $exception->details());
        $this->assertEquals('', $exception->technicalDetails());
        $this->assertEquals('not-defined', $exception->service());
        $this->assertEquals([], $exception->errors());
        $this->assertEquals([], $exception->meta());
    }

    /** @test */
    public function the_exception_can_be_converted_to_an_array()
    {
        $exception = new Exception;

        $result = $exception->toArray();

        $this->assertEquals([
            'status'  => Response::HTTP_BAD_REQUEST,
            'code'    => 'unknown_error',
            'title'   => 'An error has occurred',
            'details' => Exception::GENERIC_ERROR,
            'errors'  => [],
            'meta'    => [],
        ], Arr::except($result, 'id'));
    }

    /** @test */
    public function the_service_can_be_set()
    {
        Exception::setService('foobar');

        $exception = new Exception;

        $this->assertEquals('foobar', $exception->service());
    }

    /** @test */
    public function a_technical_message_can_be_set()
    {
        $exception = new Exception('Technical message');

        $this->assertEquals('Technical message', $exception->technicalDetails());
    }

    /** @test */
    public function additional_meta_can_be_attached_in_non_production_environments()
    {
        Env::enablePutenv();
        Env::getRepository()->set('APP_ENV', 'local');
        Exception::setService('foobar');

        $exception = new Exception('Technical Message');
        $result    = $exception->toArray();

        $this->assertEquals([
            'service'           => 'foobar',
            'technical_details' => 'Technical Message',
        ], $result['meta']);
    }

    /** @test */
    public function the_exception_id_is_always_a_unique_uuid()
    {
        $validator = new GenericValidator;
        $exception = new Exception;

        $id = $exception->id();

        $this->assertTrue($validator->validate($id));
    }
}
