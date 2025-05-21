<?php

declare(strict_types=1);

namespace Pokio;

use Closure;
use Pokio\Contracts\Result;
use Throwable;

/**
 * @template TReturn
 */
final class Promise
{
    private Result $result;

    /**
     * A list of then callbacks.
     *
     * @var array<Closure(TReturn): TReturn>
     */
    private array $then = [];

    /**
     * @var Closure(Throwable): void|null
     */
    private ?Closure $catch = null;

    /**
     * @var Closure(): void|null
     */
    private ?Closure $finally = null;

    /**
     * Creates a new promise instance.
     *
     * @param  Closure(): TReturn  $callback
     */
    public function __construct(private readonly Closure $callback, private readonly ?Closure $rescue = null)
    {
        //
    }

    public function run(): void
    {
        $runtime = Environment::runtime();

        $this->result = $runtime->defer($this->callback, $this->rescue);
    }

    /**
     * Resolves the promise.
     *
     * @return TReturn
     */
    public function resolve(): mixed
    {
        $result = null;

        try {
            $result = $this->result->get();

            foreach ($this->then as $then) {
                $result = $then($result);
            }

            return $result;
        } catch (Throwable $th) {
            if ($this->catch !== null) {
                ($this->catch)($th);

                return $result;
            }

            throw $th;
        } finally {
            if ($this->finally !== null) {
                ($this->finally)();
            }
        }
    }

    /**
     * Adds a then callback to the promise.
     *
     * @param  Closure(TReturn): TReturn  $then
     * @return Promise<TReturn>
     */
    public function then(Closure $then): self
    {
        $this->then[] = $then;

        return $this;
    }

    /**
     * Adds a catch callback to the promise.
     *
     * @param  Closure(Throwable): void  $catch
     * @return Promise<TReturn>
     */
    public function catch(Closure $catch): self
    {
        $this->catch = $catch;

        return $this;
    }

    /**
     * Adds a finally callback to the promise.
     *
     * @param  Closure(): void  $finally
     * @return Promise<TReturn>
     */
    public function finally(Closure $finally): self
    {
        $this->finally = $finally;

        return $this;
    }
}
