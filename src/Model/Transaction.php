<?php
namespace PomeloPayConnect\Model;

class Transaction
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param array $json
     * @return $this
     */
    public function fromArray(array $json)
    {
        if (array_key_exists('amount', $json) && array_key_exists('currency', $json)) {
            $this->amount = $json['amount'];
            $this->currency = $json['currency'];

            return $this;
        }

        throw new \InvalidArgumentException('amount and currency are required to sign a transaction');
    }
}
