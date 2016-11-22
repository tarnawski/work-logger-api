<?php

namespace ApiBundle\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\WebApiExtension\Context\WebApiContext;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Model\TokenInterface;
use WorkLoggerBundle\Entity\AccessToken;
use WorkLoggerBundle\Entity\Client;
use WorkLoggerBundle\Entity\RefreshToken;
use WorkLoggerBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

/**
 * Defines application features from the specific context.
 */
class ApiContext extends WebApiContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    use KernelDictionary;

    /**
     * @BeforeScenario @cleanDB
     * @AfterScenario @cleanDB
     */
    public function cleanDB()
    {
        $application = new Application($this->getKernel());
        $application->setAutoExit(false);
        $application->run(new StringInput("doctrine:schema:drop --force -n -q"));
        $application->run(new StringInput("doctrine:schema:create -n -q"));
    }

    /**
     * @return EntityManager
     */
    protected function getManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @param PyStringNode $string
     * @Then the JSON response should match:
     */
    public function theJsonResponseShouldMatch(PyStringNode $string)
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();
        $match = $matcher->match($this->response->getBody()->getContents(), $string->getRaw());
        \PHPUnit_Framework_Assert::assertTrue($match, $matcher->getError());
    }

    private function createToken(TokenInterface $token, $row)
    {
        $reflectionClass = new \ReflectionClass(get_class($token));
        $id = $reflectionClass->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($token, $row['ID']);
        $token->setToken($row['TOKEN']);
        $expiresAt = new \DateTime($row['EXPIRES_AT']);
        $token->setExpiresAt($expiresAt->getTimestamp());
        /** @var Client $client */
        $client = $this->getManager()->getReference(Client::class, $row['CLIENT']);
        $token->setClient($client);
        /** @var User $user */
        $user = $this->getManager()->getReference(User::class, $row['USER']);
        $token->setUser($user);
        $this->getManager()->persist($token);
        $metadata = $this->getManager()->getClassMetaData(get_class($token));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_CUSTOM);
    }

    /**
     * @param TableNode $table
     * @Given There are the following clients:
     */
    public function thereAreTheFollowingClients(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $client = new Client();
            $reflectionClass = new \ReflectionClass(Client::class);
            $id = $reflectionClass->getProperty('id');
            $id->setAccessible(true);
            $id->setValue($client, $row['ID']);
            $client->setRandomId($row['RANDOM_ID']);
            $client->setSecret($row['SECRET']);
            $client->setRedirectUris(explode(',', $row['URL']));
            $client->setAllowedGrantTypes(explode(',', $row['GRANT_TYPES']));

            $this->getManager()->persist($client);
        }

        $this->getManager()->flush();
        $this->getManager()->clear();
    }

    /**
     * @param TableNode $table
     * @Given there are the following users:
     */
    public function thereAreTheFollowingUsers(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $user = new User();
            $user->setFirstName($row['FIRST_NAME']);
            $user->setLastName($row['LAST_NAME']);
            $user->setUsername($row['USERNAME']);
            $user->setEmail($row['EMAIL']);
            $user->setPlainPassword($row['PASSWORD']);
            $user->setSuperAdmin(boolval($row['SUPERADMIN']));
            $user->setEnabled($row['ENABLED']);
            $user->setRoles(explode(',', $row['ROLE']));
            $this->getManager()->persist($user);
        }
        $this->getManager()->flush();
        $this->getManager()->clear();
    }

     /**
     * @param TableNode $table
     * @Given There are the following refresh tokens:
     */
    public function thereAreTheFollowingRefreshTokens(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $accessToken = new RefreshToken();
            $this->createToken($accessToken, $row);
        }
        $this->getManager()->flush();
        $this->getManager()->clear();
    }

    /**
     * @param TableNode $table
     * @Given There are the following access tokens:
     */
    public function thereAreTheFollowingAccessTokens(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $accessToken = new AccessToken();
            $this->createToken($accessToken, $row);
        }
        $this->getManager()->flush();
        $this->getManager()->clear();
    }
}
