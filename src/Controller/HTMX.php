<?php

namespace Drupal\htmx\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class HTMX extends ControllerBase {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a HTMX controller.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
    );
  }

  /**
   * @return array
   */
  public function demo() {
    return [
      '#theme' => 'htmx_demo',
      '#button_text' => $this->t('Get demo users'),
    ];
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function userData() {
    $data = $this->getUsers();

    $build = [
      '#theme' => 'htmx_demo_users',
      '#users' => $data['users'],
    ];

    $html_content = $this->renderer->renderPlain($build);

    return new Response($html_content);
  }

  /**
   * Get dummy users.
   *
   * @return array|mixed|void
   */
  private function getUsers() {
    $client = \Drupal::httpClient();
    try {
      $response = $client->request('GET', 'https://dummyjson.com/users');
      $statusCode = $response->getStatusCode();
      if ($statusCode == 200) {
        $body = $response->getBody();
        return Json::decode($body);
      }
    } catch (GuzzleException $e) {
      return [];
    }
  }

}
