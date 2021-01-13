<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\ORM;

use Db;
use Exception;

/**
 * Class DPDEntityManager
 */
class EntityManager
{
    /**
     * @var Db
     */
    private $db;

    /**
     * DPDEntityManager constructor.
     *
     * @param Db $db
     */
    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * @param string $entityClassName
     *
     * @return AbstractEntityRepository
     *
     * @throws Exception
     */
    public function getRepository($entityClassName)
    {
        if (!method_exists($entityClassName, 'getRepositoryClassName')) {
            $message = sprintf(
                'Entity "%s" must implement "%s" static method',
                $entityClassName,
                'getRepositoryClassName'
            );
            throw new Exception($message);
        }

        $repositoryClass = call_user_func([$entityClassName, 'getRepositoryClassName']);

        if (!$repositoryClass || !is_subclass_of($repositoryClass, 'AbstractEntityRepository')) {
            $message = sprintf(
                'Repository %s must extend %s class',
                $repositoryClass,
                'DPDAbstractEntityRepository'
            );
            throw new Exception($message);
        }

        $repository = new $repositoryClass($this->db);

        return $repository;
    }
}
