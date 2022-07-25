<?php declare(strict_types=1);

namespace Shopware\Core\Installer\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
abstract class InstallerController extends AbstractController
{
    private const ROUTES = [
        'installer.language-selection' => 'language-selection',
        'installer.requirements' => 'requirements',
        'installer.license' => 'license',
        'installer.database-configuration' => 'database-configuration',
        'installer.database-import' => 'database-import',
        'installer.configuration' => 'configuration',
        'installer.finish' => 'finish',
    ];

    /**
     * @param array<string, mixed> $parameters
     */
    protected function renderInstaller(string $view, array $parameters = []): Response
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request !== null) {
            $parameters['menu'] = $this->getMenuData($request);
        }

        $parameters['supportedLanguages'] = $this->getParameter('shopware.installer.supportedLanguages');
        $parameters['shopware']['version'] = $this->getParameter('kernel.shopware_version');

        return $this->render($view, $parameters);
    }

    /**
     * @return array{label: string, active: bool, isCompleted: bool}[]
     */
    private function getMenuData(Request $request): array
    {
        $currentFound = false;
        $menu = [];
        foreach (self::ROUTES as $route => $name) {
            if ($route === $request->attributes->get('_route')) {
                $currentFound = true;
            }

            $menu[] = [
                'label' => $name,
                'active' => $route === $request->attributes->get('_route'),
                'isCompleted' => !$currentFound,
            ];
        }

        return $menu;
    }
}
