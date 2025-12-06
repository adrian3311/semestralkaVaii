<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class HomeController
 * Handles actions related to the home page and other public actions.
 *
 * This controller includes actions that are accessible to all users, including a default landing page and a contact
 * page. It provides a mechanism for authorizing actions based on user permissions.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Authorizes controller actions based on the specified action name.
     *
     * In this implementation, all actions are authorized unconditionally.
     * Override this method in subclasses to implement per-action authorization
     * rules (for example, checking user roles or permissions).
     *
     * @param Request $request The current HTTP request instance (may carry user/session info).
     * @param string $action The action name to authorize (e.g. "index", "contact").
     * @return bool Returns true to allow the action, false to deny.
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Displays the default home page.
     *
     * Renders the main landing page of the application. This action typically
     * returns a ViewResponse that the framework will render into HTML.
     *
     * @param Request $request The current HTTP request.
     * @return Response The response object containing the rendered HTML for the home page.
     */
    public function index(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Displays the contact page.
     *
     * Serves the contact information view. No authorization is required and the
     * page is accessible to all visitors.
     *
     * @param Request $request The current HTTP request.
     * @return Response The response object containing the rendered HTML for the contact page.
     */
    public function contact(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Displays the information page.
     *
     * This action renders a static information view. It accepts the Request
     * instance for parity with other actions and to allow future extensions
     * (e.g. reading query parameters).
     *
     * @param Request $request The current HTTP request.
     * @return Response The response object containing the rendered HTML for the information page.
     */
    public function information(Request $request): Response
    {
        return $this->html();
    }
}
