<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd129e47fcb8ccfa4857a7e42930b1cc8
{
    public static $classMap = array (
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\PID\\AbstractPid' => __DIR__ . '/../..' . '/../classes/Helpers/PID/AbstractPid.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\PID\\Doi' => __DIR__ . '/../..' . '/../classes/Helpers/PID/Doi.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnArticleView' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnArticleView.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnConstants' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnConstants.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnDao' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnDao.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnDataModel' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnDataModel.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnSchema' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnSchema.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnWorkflowForm' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnWorkflowForm.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnWorkflowTab' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnWorkflowTab.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitd129e47fcb8ccfa4857a7e42930b1cc8::$classMap;

        }, null, ClassLoader::class);
    }
}
