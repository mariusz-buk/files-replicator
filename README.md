# File replicator for OctoberCMS
OctoberCMS FilesReplicator trait used when replicating any model with files.

## How to use
1. Define model's properties `$attachOne` and/or `$attachMany`.
2. For replicating model use `$newModel = $oldModel->replicate()`. That's all.
3. If you want update existing model with files from other model (of the same class) use `$myModel->replicateFilesFrom($otherModel)`.

## About this code
I found it would be useful to replicate files when we replicate model. I saw more coders look for solution so here it is. I hope you'll find it easy to us.

Any questions/suggestions are welcome.

I provide commercial support for your projects.