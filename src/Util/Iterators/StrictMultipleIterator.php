<?php

namespace IterTools\Util\Iterators;

/**
 * @internal
 * Works like \MultipleIterator
 * but throws \LengthException if at least one iterator ends before the others while iteration process.
 *
 * {@inheritDoc}
 */
class StrictMultipleIterator extends \MultipleIterator
{
    /**
     * Throws \LengthException if at least one iterator ends before the others.
     *
     * {@inheritDoc}
     *
     * @throws \LengthException
     */
    public function valid(): bool
    {
        // If is valid by default:
        if (parent::valid() === true) {
            // No extra checks required.
            return true;
        }

        // So we know that the iterator is invalid.
        // And this has two possible reasons:
        // 1) all iterators ended (regular situation);
        // 2) at least one iterator ended and at least one iterator don't ended (irregular situation).

        // If flag MIT_NEED_ALL has been set:
        if (($flags = $this->getFlags()) & \MultipleIterator::MIT_NEED_ALL) {
            // We are backing up current flags and replacing them with MIT_NEED_ANY to do an extra check
            $this->setFlags(\MultipleIterator::MIT_NEED_ANY);

            /** So do we have non-ended iterators? @phpstan-ignore-next-line */
            if (parent::valid() === true) {
                // This is situation (2)
                throw new \LengthException('Iterables of unequal sizes');
            }

            // Restoring flags.
            $this->setFlags($flags);
        }

        return false;
    }
}
