<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Functional\Bundle\BenchmarkBundle\Controllers\Backend;

class BenchmarkOverviewControllerTest extends BenchmarkControllerTestCase
{
    const CONTROLLER_NAME = \Shopware_Controllers_Backend_BenchmarkOverview::class;

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_local_start()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkLocalOverview/render/template/start', $redirect);
    }

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_industry_select()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('terms_accepted', 1);

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkLocalOverview/render/template/industry_select', $redirect);
    }

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_cached_fresh_statistics()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('terms_accepted', 1);
        $this->setSetting('industry', 1);
        $this->setSetting('last_received', date('Y-m-d H:i:s'));
        $this->setSetting('cached_template', '<h2>Placeholder</h2>');

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkOverview/render', $redirect);
    }

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_local_fresh_statistics_no_file()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('terms_accepted', 1);
        $this->setSetting('industry', 1);
        $this->setSetting('last_received', date('Y-m-d H:i:s'));

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkLocalOverview/render/template/statistics', $redirect);
    }

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_local_inactive()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('terms_accepted', 1);
        $this->setSetting('industry', 1);
        $this->setSetting('active', 0);

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkLocalOverview/render/template/statistics', $redirect);
    }

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_local_active_outdated()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('terms_accepted', 1);
        $this->setSetting('industry', 1);
        $this->setSetting('last_received', date('Y-m-d H:i:s', strtotime('-31 days')));
        $this->setSetting('active', 1);

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkLocalOverview/render/template/statistics', $redirect);
    }

    /**
     * @group BenchmarkBundle
     */
    public function testIndexAction_should_redirect_cached_active()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('terms_accepted', 1);
        $this->setSetting('industry', 1);
        $this->setSetting('last_received', date('Y-m-d H:i:s', strtotime('-3 days')));
        $this->setSetting('active', 1);
        $this->setSetting('cached_template', '<h2>Placeholder</h2>');

        $controller->indexAction();

        $redirect = $this->getRedirect($controller->Response());

        $this->assertContains('BenchmarkOverview/render', $redirect);
    }

    public function testRenderAction_should_render_cached_template()
    {
        /** @var \Shopware_Controllers_Backend_BenchmarkOverview $controller */
        $controller = $this->getController();

        $this->installDemoData('benchmark_config');
        $this->setSetting('cached_template', '<h2>Placeholder</h2>');

        $this->expectOutputString('<h2>Placeholder</h2>');
        $controller->renderAction();
    }

    /**
     * @param \Enlight_Controller_Response_ResponseHttp $response
     *
     * @return string
     */
    private function getRedirect(\Enlight_Controller_Response_ResponseHttp $response)
    {
        return $response->getHeader('Location');
    }
}