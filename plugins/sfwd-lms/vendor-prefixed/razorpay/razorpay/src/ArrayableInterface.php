<?php
/**
 * @license MIT
 *
 * Modified by learndash on 04-September-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\Razorpay\Api;

interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}