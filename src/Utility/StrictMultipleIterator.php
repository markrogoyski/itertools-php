<?php

namespace IterTools\Utility;

/**
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
        if ($isValid = parent::valid()) {
            // No extra checks required.
            return $isValid;
        }

        // So we know that the iterator is invalid.
        // And this has two possible reasons:
        // 1) all iterators ended (regular situation);
        // 2) at least one iterator ended and at least one iterator don't ended (irregular situation).

        // If flag MIT_NEED_ALL has been set:
        if (($flags = $this->getFlags()) & \MultipleIterator::MIT_NEED_ALL) {
            // We are backuping current flags and replacing them with MIT_NEED_ANY
            // to do an extra check
            $this->setFlags(\MultipleIterator::MIT_NEED_ANY);

            // So do we have non-ended iterators?
            $isReallyValid = parent::valid();

            if ($isValid !== $isReallyValid) {
                // This is situation (2)
                throw new \LengthException();
            }

            // Restoring flags.
            $this->setFlags($flags);
        }

        return $isValid;
    }
}
