<?php

namespace Claroline\CoreBundle\Controller\Tool;

use Claroline\CoreBundle\Library\Testing\FunctionalTestCase;
use Claroline\CoreBundle\Tests\DataFixtures\LoadWorkspaceData;

class CalendarControllerTest extends FunctionalTestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->loadUserFixture();
        $this->loadFixture(new LoadWorkspaceData);
    }

    public function testWorkspaceUserCanSeeTheAgenda()
    {

        $workspaceId = $this->getFixtureReference('workspace/ws_a')->getId();
        $this->logUser($this->getFixtureReference('user/ws_creator'));
        $this->client->request('GET', "/workspaces/{$workspaceId}/open/tool/calendar");
        $status = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $status);
    }

    public function testAddEventCalendar()
    {
        $workspaceId = $this->getFixtureReference('workspace/ws_a')->getId();
        $this->logUser($this->getFixtureReference('user/ws_creator'));
        $this->client->request(
            'POST',
            "/workspaces/tool/calendar/{$workspaceId}/add",
            array(
                'calendar_form' => array(
                    'title' => 'foo',
                    'description' => 'ghhkkgf',
                    'end' => array(
                        'day' => '24',
                        'month' => '1',
                        'year' => '2013'
                    ),
                    'allDay' => TRUE,
                    'priority' => '#000000'
                   ),
                  'date' => 'Thu Jan 24 2013 00:00:00 GMT+0100'
                )
        );

        $status = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $status);
    }

    public function testDeleteEventCalendar()
    {
        $workspaceId = $this->getFixtureReference('workspace/ws_a')->getId();
        $this->logUser($this->getFixtureReference('user/ws_creator'));
        $this->client->request(
            'POST',
            "/workspaces/tool/calendar/{$workspaceId}/add",
            array(
                'calendar_form' => array(
                    'title' => 'foo',
                    'description' => 'ghhkkgf',
                    'end' => array(
                        'day' => '24',
                        'month' => '1',
                        'year' => '2013'
                    ),
                    'allDay' => TRUE
                   ),
                  'date' => 'Thu Jan 24 2013 00:00:00 GMT+0100'
                )
        );

        $data = $this->client->getResponse()->getContent();
        $data = json_decode($data, true);
        $this->client->request(
            'POST',
            "/workspaces/tool/calendar/{$workspaceId}/delete",
            array(
                    'id' => $data['id']
                )
        );

        $status = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $status);

    }

    public function testUpdateEventCalendar()
    {
        $workspaceId = $this->getFixtureReference('workspace/ws_a')->getId();
        $this->logUser($this->getFixtureReference('user/ws_creator'));
        $this->client->request(
            'POST',
            "/workspaces/tool/calendar/{$workspaceId}/add",
            array(
                'calendar_form' => array(
                    'title' => 'foo',
                    'description' => 'ghhkkgf',
                    'end' => array(
                        'day' => '24',
                        'month' => '1',
                        'year' => '2013'
                    ),
                    'allDay' => TRUE
                   ),
                  'date' => 'Thu Jan 24 2013 00:00:00 GMT+0100'
                )
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $dataForm = array(
                'id' => $content['id'],
                'dayDelta' => '1',
                'minuteDelta' => '0'
                );
        $this->client->request(
            'POST',
            "/workspaces/tool/calendar/{$workspaceId}/move",
            $dataForm
        );
        //var_dump( $this->client->getResponse()->getContent());
        $contentUpdate = json_decode($this->client->getResponse()->getContent());
        $status = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $status);
        $this->client->request('GET', "/workspaces/tool/calendar/{$workspaceId}/show");
        $listEvents = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(array($contentUpdate), $listEvents);
    }
}
