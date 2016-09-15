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
                $graphNode = $this->getFacebookBaseService()->fetchPage($id, "*");
                $pageWidget->setPageID($id);

                $graphEdge = array();
                $graphEdge['key'] = "albums";
                $graphEdge['count'] = $pageWidget->fetchAllAlbums()->count();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "events";
                $graphEdge['count'] = $pageWidget->fetchAllEvents()->count();
                $graphEdges[] = $graphEdge;

                $graphEdge = array();
                $graphEdge['key'] = "milestones";
                $graphEdge['count'] = $pageWidget->fetchAllMilestones()->count();
                $graphEdges[] = $graphEdge;

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
                break;
            case "post":
                $graphNode = $this->getFacebookBaseService()->fetchPost($id, "*");

                $graphEdge = array();
                $graphEdge['key'] = "likes";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchLikes($id)->count();
                $graphEdges[] = $graphEdge;
                $graphNodeTitle = $graphNode->getField("id");
                break;
            case "photo":
                $graphNode = $this->getFacebookBaseService()->fetchPhoto($id, "*");
                break;
            case "video":
                $graphNode = $this->getFacebookBaseService()->fetchVideo($id, "*");

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
                $graphNode = $this->getFacebookBaseService()->fetchMilestone($id, "*");
                $graphNodeTitle = $graphNode->getField("title");
                break;
            case "album":
                $graphNode = $this->getFacebookBaseService()->fetchAlbum($id, "*");

                $graphEdge = array();
                $graphEdge['key'] = "photos";
                $graphEdge['count'] = $this->getFacebookBaseService()->fetchPhotosByAlbum($id,"*")->count();
                $graphEdges[] = $graphEdge;
                break;
            case "event":
                $graphNode = $this->getFacebookBaseService()->fetchEvent($id, "*");
                break;
            default : var_dump($nodetype);

        }

        $graphNodeType = $this->camelize($nodetype);


        var_dump($this->params()->fromRoute());
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

        $edge = $this->params()->fromRoute("edge");

        $subclass = null;
        switch ($edge) {
            case "events":
                $graphEdge = $this->getFacebookBaseService()->fetchEvents($id, "*");
                break;
            case "albums":
                $graphEdge = $this->getFacebookBaseService()->fetchAlbums($id, "*");
                break;
            case "photos":
                $graphEdge = $this->getFacebookBaseService()->fetchPhotosByAlbum($id, "*");
                break;
            case "posts":
                $graphEdge = $this->getFacebookBaseService()->fetchPosts($id, "*");
                break;
            case "milestones":
                $graphEdge = $this->getFacebookBaseService()->fetchMilestones($id, "*");
                break;
            case "videos":
                $graphEdge = $this->getFacebookBaseService()->fetchVideos($id, "*");
                break;
            case "likes":
                $graphEdge = $this->getFacebookBaseService()->fetchLikes($id);
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

