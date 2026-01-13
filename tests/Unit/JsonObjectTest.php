<?php

namespace Amadul\JsonObject\Tests\Unit;

use Amadul\JsonObject\JsonObject;
use Amadul\JsonObject\Tests\TestCase;
use Amadul\JsonObject\Concerns\HasAccessors;
use Amadul\JsonObject\Concerns\HasValidation;
use Amadul\JsonObject\Concerns\TracksDirtyAttributes;
use Amadul\JsonObject\Contracts\ValidatesJson;

class JsonObjectTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_from_array()
    {
        $obj = TestObject::from(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $obj->toArray());
    }

    /** @test */
    public function it_can_access_attributes()
    {
        $obj = TestObject::from(['foo' => 'bar']);
        $this->assertEquals('bar', $obj->get('foo'));
        
        $obj->set('foo', 'baz');
        $this->assertEquals('baz', $obj->get('foo'));
    }

    /** @test */
    public function it_casts_attributes()
    {
        $obj = TestObject::from(['count' => '123']);
        $this->assertSame(123, $obj->get('count'));
    }

    /** @test */
    public function it_validates()
    {
        $obj = TestObject::from(['foo' => 'bar']);
        // Should not throw
        $obj->validate();

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $invalid = TestObject::from(['foo' => '']);
        $invalid->validate();
    }

    /** @test */
    public function it_tracks_dirty()
    {
        $obj = TestObject::from(['foo' => 'bar']);
        $this->assertEmpty($obj->dirty());

        $obj->set('foo', 'baz');
        $this->assertEquals(['foo' => 'baz'], $obj->dirty());
    }
}

class TestObject extends JsonObject implements ValidatesJson
{
    use HasAccessors, HasValidation, TracksDirtyAttributes;

    protected array $casts = [
        'count' => 'integer',
    ];

    public function rules(): array
    {
        return [
            'foo' => 'required',
        ];
    }
}
