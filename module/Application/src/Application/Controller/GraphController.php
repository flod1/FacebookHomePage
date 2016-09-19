<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class GraphController extends AbstractActionController
{
    /**
     * @var \FbBasic\Service\FacebookBase
     */
    protected $facebookbaseService;

    /**
     * Getters/setters for DI stuff
     */

    /**
     * @return \FbBasic\Service\FacebookBase
     */
    public function getFacebookBaseService()
    {
        if (!$this->facebookbaseService) {
            $this->facebookbaseService = $this->serviceLocator->get('fbbasic_graph_service');
        }
        return $this->facebookbaseService;
    }

    public function setFacebookBaseService(\FbBasic\Service\FacebookBase $facebookbaseService)
    {
        $this->facebookbaseService = $facebookbaseService;
        return $this;
    }

    public function nodeAction()
    {

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $pageWidget = $viewHelperManager->get('pageWidget');
        //$graphWidget = $viewHelperManager->get('graphWidget');

        /**
         * @var $pageWidget \FbPage\View\Helper\PageWidget
         * @var $graphWidget \FbBasic\View\Helper\GraphWidget
         */


        $id = $this->params()->fromRoute("id");
        $fields = $this->params()->fromQuery("fields","*");
        $parameters = array("metadata" => 1);
        $response = $this->getFacebookBaseService()->fetchGraphNode($id, $parameters);
        $graphNode = $response->getGraphNode();

        /** @var $metadata \Facebook\GraphNodes\GraphNode */
        $metadata = $graphNode->getField("metadata");
        $nodetype = $metadata->getField("type");

        $graphEdges = null;


        $graphNodeTitle = $graphNode->getField("name");

        switch ($nodetype) {
            case "page":
                $graphNode = $this->getFacebookBaseService()->fetchPage($id, $fields);
                $pageWidget->setPageID($id);
/*
                $graphEdge = array();
                $graphEdge['key'] = "albums";
                $graphEdge['count'] = $pageWidget->fetchAllAlbums()->count();
                //$graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "events";
                $graphEdge['count'] = $pageWidget->fetchAllEvents()->count();
                //$graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "milestones";
                $graphEdge['count'] = $pageWidget->fetchAllMilestones()->count();
                //$graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "posts";
                $graphEdge['count'] = $pageWidget->fetchAllPosts()->count();
                $graphEdges[] = $graphEdge;
                $graphEdge = array();
                $graphEdge['key'] = "global_brand_children";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchGlobalBrandChildren($id)->count();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "videos";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchVideos($id)->count();
                $graphEdges[] = $graphEdge;
*/
                break;
            case "post":
                $graphNode = $this->getFacebookBaseService()->fetchPost($id, $fields);
                $graphNodeTitle = $graphNode->getField("id");
                break;
            case "user":
                $graphNode = $this->getFacebookBaseService()->fetchUser($id, $fields);
                break;
            case "photo":
                $graphNode = $this->getFacebookBaseService()->fetchPhoto($id, $fields);
                $graphNodeTitle = $graphNode->getField("id");
                break;
            case "comment":
                $graphNode = $this->getFacebookBaseService()->fetchComment($id, $fields);
                $graphNodeTitle = $graphNode->getField("id");
                break;
            case "video":
                $graphNode = $this->getFacebookBaseService()->fetchVideo($id, $fields);

                $graphEdge = array();
                $graphEdge['key'] = "likes";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchLikesByVideo($id)->count();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "comments";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchCommentsByVideo($id)->count();
                $graphEdges[] = $graphEdge;

                $graphNodeTitle = $graphNode->getField("id");

                break;
            case "page_milestone":
                $graphNode = $this->getFacebookBaseService()->fetchMilestone($id, $fields);
                $graphNodeTitle = $graphNode->getField("title");
                break;
            case "album":
                $graphNode = $this->getFacebookBaseService()->fetchAlbum($id, $fields);
/*
                $graphEdge = array();
                $graphEdge['key'] = "photos";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchPhotosByAlbum($id,$fields)->count();
                $graphEdge['edge'] = $this->getFacebookBaseService()->fetchPhotosByAlbum($id,$fields);
                //$graphEdges[] = $graphEdge;
*/
                break;
            case "event":
                $graphNode = $this->getFacebookBaseService()->fetchEvent($id, $fields);

                $graphEdge = array();
                $graphEdge['key'] = "attending";
                $graphEdge['count'] = $graphNode->getAttendingCount();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "declined";
                $graphEdge['count'] = $graphNode->getDeclinedCount();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "interested";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchInterestedsByEvent($id)->count();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "maybe";
                $graphEdge['count'] = $graphNode->getMaybeCount();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "noreply";
                $graphEdge['count'] = $graphNode->getNoreplyCount();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "comments";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchComments($id)->count();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "photos";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchPhotos($id)->count();
                $graphEdges[] = $graphEdge;

                break;
            case "group":
                $graphNode = $this->getFacebookBaseService()->fetchGroup($id, $fields);

                break;
            default : var_dump($nodetype);

        }

        $graphNodeType = $this->camelize($nodetype);


        //var_dump($this->params()->fromRoute());
        return new ViewModel(array("graphNode" => $graphNode, "graphEdges" => $graphEdges, "graphNodeType" => $graphNodeType,"graphNodeTitle"=>$graphNodeTitle));

    }

    function camelize($input, $separator = '_')
    {
        return str_replace($separator, ' ', ucwords($input, $separator));
    }

    public function edgeAction()
    {

        $id = $this->params()->fromRoute("id");
        $parameters = array("metadata" => 1);
        $response = $this->getFacebookBaseService()->fetchGraphNode($id, $parameters);
        $graphNode = $response->getGraphNode();

        /** @var $metadata \Facebook\GraphNodes\GraphNode */
        $metadata = $graphNode->getField("metadata");
        $graphEdgeTitle = $graphNode->getField("id");
        $nodetype = $metadata->getField("type");

        switch ($nodetype) {
            case "page":
            case "album":
            case "event":
                $graphEdgeTitle = $graphNode->getField("name");
                break;
        }

        $edge = $this->params()->fromRoute("edge");
        $fields = $this->params()->fromQuery("fields","*");

        $subclass = null;
        switch ($edge) {
            case "events":
                $graphEdge = $this->getFacebookBaseService()->fetchEvents($id, $fields);
                break;
            case "albums":
                $graphEdge = $this->getFacebookBaseService()->fetchAlbums($id, $fields);
                break;
            case "feed":
                $graphEdge = $this->getFacebookBaseService()->fetchPosts($id, $fields);
                break;
            case "photos":
                $graphEdge = $this->getFacebookBaseService()->fetchPhotosByAlbum($id, $fields);
                break;
            case "posts":
                $graphEdge = $this->getFacebookBaseService()->fetchPosts($id, $fields);
                break;
            case "milestones":
                $graphEdge = $this->getFacebookBaseService()->fetchMilestones($id, $fields);
                break;
            case "videos":
                $graphEdge = $this->getFacebookBaseService()->fetchVideos($id, $fields);
                break;
            case "likes":
                $graphEdge = $this->getFacebookBaseService()->fetchLikes($id,$fields);
                break;
            case "interested":
                $graphEdge = $this->getFacebookBaseService()->fetchInterestedsByEvent($id);
                break;
            //case "photos":$subclass = "GraphPicture";break;
            default :
                $graphEdge = $this->getFacebookBaseService()->fetchGraphEdge($id, $edge, $subclass, $parameters);
        }

        $graphNodeType = ucfirst($nodetype);
        $graphEdgeType = ucfirst($edge);

        return new ViewModel(array("graphNode" => $graphNode, "graphEdge" => $graphEdge, "graphNodeType" => $graphNodeType, "graphEdgeType" => $graphEdgeType,"graphEdgeTitle"=>$graphEdgeTitle));

    }

}

